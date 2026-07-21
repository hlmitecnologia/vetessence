#!/usr/bin/env python3
"""Motor de gravação de vídeos de treinamento — VetEssence.

Uso: python3 treinamento.py --modulo 11-tutores-pets
     python3 treinamento.py --list        (lista módulos)
"""

import argparse
import json
import os
import re
import signal
import subprocess
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from roteiros import CATALOGO, listar_modulos

# ── Configuração ──────────────────────────────────────────────────────────────
DISPLAY_ENV = os.environ.get("DISPLAY", ":1")
OUTPUT_DIR = Path.home() / "Videos" / "VetEssence"
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
TIMESTAMP = time.strftime("%Y%m%d_%H%M%S")

modulo_info = None  # será setado pelo main
VIDEO_FILE = None
ffmpeg_proc = None


# ── Captura de tela via ffmpeg x11grab ──────────────────────────────────────

def iniciar_gravacao():
    global ffmpeg_proc, VIDEO_FILE
    nome_modulo = modulo_info["arquivo"]
    VIDEO_FILE = str(OUTPUT_DIR / f"{nome_modulo}_{TIMESTAMP}.mp4")
    cmd = [
        "ffmpeg",
        "-y",
        "-f", "x11grab",
        "-video_size", "1920x1080",
        "-framerate", "30",
        "-i", f"{DISPLAY_ENV}.0",
        "-c:v", "libx264",
        "-preset", "medium",
        "-crf", "18",
        "-pix_fmt", "yuv420p",
        "-movflags", "+faststart",
        "-an",
        VIDEO_FILE,
    ]
    ffmpeg_proc = subprocess.Popen(
        cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL
    )
    print(f"  [ffmpeg] Gravação iniciada → {VIDEO_FILE}")
    return True


def parar_gravacao():
    global ffmpeg_proc
    if ffmpeg_proc is None:
        return None
    ffmpeg_proc.send_signal(signal.SIGINT)
    try:
        ffmpeg_proc.wait(timeout=5)
    except subprocess.TimeoutExpired:
        ffmpeg_proc.kill()
        ffmpeg_proc.wait()
    size = Path(VIDEO_FILE).stat().st_size if Path(VIDEO_FILE).exists() else 0
    print(f"  [ffmpeg] Gravação finalizada ({size // 1024} KB)")
    ffmpeg_proc = None
    return VIDEO_FILE


# ── Helpers Selenium para Livewire / TomSelect ──────────────────────────────

def preencher_livewire(driver, wire_model, valor):
    """Preenche um campo Livewire (wire:model) via JS."""
    # Busca primeiro em modal aberto (prioritário) para evitar conflitos com
    # campos de mesmo nome na página principal (ex: species no drug-formulary
    # e no dosage-calculator). Fallback para o documento todo.
    ok = driver.execute_script(f"""
        function buscarEl(container) {{
            var els = container.querySelectorAll('input, select, textarea');
            for (var i = 0; i < els.length; i++) {{
                var m = els[i].getAttribute('wire:model') || els[i].getAttribute('wire:model.live') || els[i].getAttribute('wire:model.blur') || els[i].getAttribute('wire:model.defer');
                if (m === '{wire_model}') return els[i];
            }}
            return null;
        }}
        // Procura primeiro dentro de modal visível
        var modal = document.querySelector('.modal.show');
        var el = modal ? buscarEl(modal) : null;
        // Se não achou no modal, busca no documento todo
        if (!el) el = buscarEl(document);
        if (!el) return 'not_found';

        var component = null;
        var lwEl = el.closest('[wire\\\\:id]');
        if (lwEl) {{
            var compId = lwEl.getAttribute('wire:id');
            if (compId) component = Livewire.find(compId);
        }}

        var tag = el.tagName.toLowerCase();
        if (tag === 'select') {{
            for (var i = 0; i < el.options.length; i++) {{
                if (el.options[i].value === arguments[0] || el.options[i].text.indexOf(arguments[0]) !== -1) {{
                    el.value = el.options[i].value;
                    el.dispatchEvent(new Event('change', {{bubbles: true}}));
                    if (component) component.set('{wire_model}', el.options[i].value);
                    return 'ok';
                }}
            }}
            return 'no_option';
        }}
        el.value = arguments[0];
        el.dispatchEvent(new Event('input', {{bubbles: true}}));
        el.dispatchEvent(new Event('change', {{bubbles: true}}));
        if (component) component.set('{wire_model}', arguments[0]);
        return 'ok';
    """, valor)
    if ok == 'not_found':
        print(f"    ⚠️  Elemento wire:model={wire_model} não encontrado")
    elif ok == 'no_option':
        print(f"    ⚠️  Opção '{valor}' não encontrada no select {wire_model}")
    time.sleep(0.5)


def selecionar_tom_select(driver, wire_model, label_texto):
    """Seleciona opção no TomSelect usando sua API JS interna.

    Encontra o <select> original (data-wire ou name), acessa a instância
    TomSelect via `select.tomselect`, abre o dropdown e seleciona a opção.
    """
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    try:
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".ts-wrapper"))
        )
    except Exception:
        pass

    # Usa a API JS do TomSelect: abre o dropdown, encontra a opção pelo texto,
    # e seleciona via setValue. Dispensa navegação no DOM do ts-wrapper.
    resultado = driver.execute_script(f"""
        var select = document.querySelector('select[data-wire="{wire_model}"]')
                 || document.querySelector('select[name="{wire_model}"]');
        if (!select) return 'no_select';
        var ts = select.tomselect;
        if (!ts) return 'no_tomselect';

        var val = null;
        for (var key in ts.options) {{
            var opt = ts.options[key];
            if (opt.text.indexOf(arguments[0]) !== -1) {{
                val = key;
                break;
            }}
        }}
        if (val === null) {{
            var keys = Object.keys(ts.options);
            if (keys.length > 0) {{ val = keys[0]; }}
        }}
        if (val === null) return 'no_option';

        ts.open();
        ts.setValue(val);
        ts.close();
        ts.blur();

        // MutationObserver: fecha dropdown se Livewire reabrir após morph
        var dd = ts.dropdown;
        var obs = new MutationObserver(function() {{
            if (ts.isOpen) {{ ts.close(); ts.blur(); }}
        }});
        obs.observe(dd, {{attributes: true, attributeFilter: ['style', 'class']}});
        setTimeout(function() {{ obs.disconnect(); }}, 3000);

        return 'ok';
    """, label_texto)
    time.sleep(1)

    # Garante que o valor foi sincronizado via Livewire API (só para data-wire)
    driver.execute_script(f"""
        (function() {{
            var select = document.querySelector('select[data-wire="{wire_model}"]');
            if (!select) return;
            var p = select.parentElement;
            while (p) {{
                var wid = p.getAttribute ? p.getAttribute('wire:id') : null;
                if (wid) {{
                    var c = Livewire.find(wid);
                    if (c) {{
                        c.set('{wire_model}', select.value);
                    }}
                    break;
                }}
                p = p.parentElement;
            }}
        }})();
    """)
    time.sleep(0.5)


def achar_lw_component(driver, container_selector):
    """Encontra o ID do componente Livewire dentro de um container, retornando (cid, state_str)."""
    return driver.execute_script(f"""
        var container = document.querySelector('{container_selector}');
        if (!container) return null;
        // Busca elemento com wire:id entre todos os descendentes
        var all = container.querySelectorAll('*');
        var lwEl = null;
        for (var i = 0; i < all.length; i++) {{
            if (all[i].getAttribute('wire:id')) {{
                lwEl = all[i];
                break;
            }}
        }}
        if (!lwEl) return null;
        var cid = lwEl.getAttribute('wire:id');
        var c = Livewire.find(cid);
        if (!c) return null;
        var state = {{}};
        ['name','tutor_id','species','gender','size','color','birth_date'].forEach(function(k) {{
            state[k] = c.get(k);
        }});
        return JSON.stringify({{cid: cid, state: state}});
    """)

def scroll_smoothly_modal(driver, modal_id, qtd_passos=12, intervalo=0.25):
    """Rola o conteúdo do modal suavemente (visível no vídeo), mesmo se o botão já estiver à vista."""
    driver.execute_script(f"""
        (function() {{
            var modal = document.querySelector('{modal_id}');
            if (!modal) return;
            var body = modal.querySelector('.modal-body') ||
                       modal.querySelector('.card-body') ||
                       modal.querySelector('.modal-content');
            if (!body) return;
            // Sempre rola pelo menos 120 px para o movimento aparecer no vídeo
            var alturaVisivel = body.clientHeight;
            var maxScroll = body.scrollHeight - alturaVisivel;
            var btn = modal.querySelector('button[type="submit"]');
            var alvo = 0;
            if (btn) {{
                alvo = btn.offsetTop - body.offsetTop - alturaVisivel + 80;
                if (alvo < 0) alvo = 0;
                if (alvo > maxScroll) alvo = maxScroll;
            }}
            if (alvo < 120) alvo = 120;  // garante movimento visível
            if (alvo > maxScroll) alvo = maxScroll;
            var inc = (alvo - body.scrollTop) / {qtd_passos};
            if (inc <= 0) inc = Math.min(120, maxScroll) / {qtd_passos};
            var i = 0;
            function passo() {{
                if (i >= {qtd_passos}) return;
                body.scrollBy({{top: inc, behavior:'instant'}});
                i++;
                setTimeout(passo, {int(intervalo * 1000)});
            }}
            passo();
        }})();
    """)
    time.sleep(qtd_passos * intervalo + 0.5)


def clicar_submit_modal(driver, modal_id):
    """Submete formulário Livewire dentro de um modal."""
    estado = achar_lw_component(driver, modal_id)
    print(f"    🐛 LW state: {estado}")

    # Scroll suave do modal (sempre visível no vídeo)
    scroll_smoothly_modal(driver, modal_id)

    # Submit via Livewire API direta (única tentativa, sem retry)
    driver.execute_script(f"""
        (function() {{
            var modal = document.querySelector('{modal_id}');
            if (!modal) return;
            var all = modal.querySelectorAll('*');
            var lwEl = null;
            for (var i = 0; i < all.length; i++) {{
                if (all[i].getAttribute('wire:id')) {{ lwEl = all[i]; break; }}
            }}
            if (!lwEl) return;
            var c = Livewire.find(lwEl.getAttribute('wire:id'));
            if (c && c.call) c.call('save');
        }})();
    """)

    # Espera modal fechar (save dispara close-modal, que fecha o modal)
    time.sleep(3)


def verificar_erro_laravel(driver):
    """Verifica se a página atual contém erro Laravel/Symfony e aborta."""
    html = driver.page_source.lower()
    indicadores = [
        'whoops!',
        'exception',
        'method not allowed',
        'symfony\\component\\httpkernel\\exception',
        'laravel\\framework',
        '/home/hector/workspace/vetessence/app/',
        'in <b>',       # parte de stack trace formatada
        '#0 </b>',      # stack trace linha
    ]
    for ind in indicadores:
        if ind in html:
            print(f"\n❌ ERRO LARAVEL DETECTADO! Indicador: '{ind}'")
            print(f"   URL: {driver.current_url}")
            driver.save_screenshot(str(Path("/tmp/treinamento_screenshots") / "ERRO_LARAVEL.png"))
            raise RuntimeError(f"Laravel error detected: '{ind}' at {driver.current_url}")


def esperar_elemento(driver, seletor, tempo=10):
    """Espera um elemento aparecer no DOM."""
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    wait = WebDriverWait(driver, tempo)
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, seletor)))


# ── Execução do Roteiro ─────────────────────────────────────────────────────

def executar_roteiro(driver, passos):
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    wait = WebDriverWait(driver, 15)
    screenshots_dir = Path("/tmp/treinamento_screenshots")
    screenshots_dir.mkdir(exist_ok=True)

    try:
        for passo in passos:
            legenda = passo.get("legenda", passo.get("texto", ""))
            passo_num = passo["passo"]
            print(f"\n  [{passo_num}] {legenda}")

            acao = passo["acao"]

            if acao == "navegar":
                driver.get(passo["url"])
                time.sleep(1)

            elif acao == "preencher":
                el = wait.until(EC.presence_of_element_located(
                    (By.CSS_SELECTOR, passo["seletor"])))
                tag = driver.execute_script("return arguments[0].tagName;", el)
                input_type = driver.execute_script(
                    "return (arguments[0].tagName.toLowerCase()==='input' ? arguments[0].type : '');", el)
                visible = el.is_displayed()
                if tag.lower() == "select":
                    driver.execute_script(
                        "arguments[0].value = arguments[1]; "
                        "arguments[0].dispatchEvent(new Event('change', {bubbles: true}));"
                        "arguments[0].dispatchEvent(new Event('input', {bubbles: true}));"
                        "arguments[0].onchange && arguments[0].onchange();",
                        el, passo["valor"])
                elif input_type == "date":
                    # Date input: locale quebra send_keys, usar JS
                    driver.execute_script(
                        "arguments[0].value = arguments[1]; "
                        "arguments[0].dispatchEvent(new Event('change', {bubbles: true}));",
                        el, passo["valor"])
                elif not visible:
                    # Campo oculto (wysiwyg, TinyMCE, etc.)
                    # Seta tanto o textarea quanto o editor TinyMCE (se existir)
                    driver.execute_script("""
                        var el = arguments[0];
                        var val = arguments[1];
                        el.value = val;
                        el.dispatchEvent(new Event('input', {bubbles: true}));
                        el.dispatchEvent(new Event('change', {bubbles: true}));
                        // Seta no TinyMCE se o editor existir
                        if (el.id && typeof tinymce !== 'undefined') {
                            var ed = tinymce.get(el.id);
                            if (ed) ed.setContent(val);
                        }
                    """, el, passo["valor"])
                else:
                    el.clear()
                    el.send_keys(passo["valor"])
                time.sleep(0.3)

            elif acao == "livewire":
                preencher_livewire(driver, passo["wire_model"], passo["valor"])

            elif acao == "tom_select":
                selecionar_tom_select(driver, passo["wire_model"], passo["valor"])

            elif acao == "clicar":
                el = wait.until(EC.presence_of_element_located(
                    (By.CSS_SELECTOR, passo["seletor"])))
                url_before = driver.current_url
                tag = driver.execute_script("return arguments[0].tagName.toLowerCase();", el)
                is_button = tag in ('button', 'input')

                if is_button:
                    has_wire_click = driver.execute_script(
                        "return arguments[0].hasAttribute ? arguments[0].hasAttribute('wire:click') : false;", el)
                    btn_type = (driver.execute_script(
                        "return (arguments[0].type || '').toLowerCase();", el) or '').lower()

                    # Detecta se o botão está dentro de form Livewire (wire:submit)
                    livewire_method = driver.execute_script("""
                        var btn = arguments[0];
                        // wire:click no próprio botão
                        var wc = btn.getAttribute('wire:click');
                        if (wc) return wc;
                        // wire:submit.prevent="method" no form ancestral (só para submit button)
                        var btn_type = (btn.type || '').toLowerCase();
                        if (btn_type === 'submit' || (btn.tagName === 'BUTTON' && btn_type !== 'checkbox' && btn_type !== 'radio')) {
                            var f = btn.form;
                            while (f && f.tagName !== 'FORM') f = f.parentElement;
                            if (f) {
                                var ws = f.getAttribute('wire:submit') ||
                                         f.getAttribute('wire:submit.prevent');
                                if (ws) return ws.trim();
                            }
                        }
                        return null;
                    """, el)

                    if livewire_method:
                        driver.execute_script("arguments[0].scrollIntoView({behavior:'instant', block:'center'});", el)
                        time.sleep(0.3)
                        result = driver.execute_script("""
                            var lw = arguments[0].closest('[wire\\\\:id]');
                            if (!lw) return 'no_component';
                            var cid = lw.getAttribute('wire:id');
                            var comp = Livewire.find(cid);
                            if (!comp) return 'no_comp';
                            var method = arguments[1];
                            return new Promise(function(resolve) {
                                var p = comp.call(method);
                                setTimeout(function() { resolve('timeout'); }, 10000);
                                if (p && typeof p.then === 'function') {
                                    p.then(function(r) { resolve('ok'); })
                                     .catch(function(e) { resolve('error:' + String(e).substring(0,100)); });
                                } else {
                                    resolve('result:' + String(p));
                                }
                            });
                        """, el, livewire_method)
                        time.sleep(3)
                        url_after = driver.current_url
                        if url_before == url_after:
                            print(f"    ⚠️ clicar: URL não mudou ({url_before[:60]})")
                            print(f"    Livewire call result: {result}")
                        else:
                            print(f"    ✅ clicar: redirect {url_before} → {url_after[:60]}")
                        continue

                    # Botão em form HTML padrão: requestSubmit nativo
                    is_form_submit = (tag == 'input' and btn_type in ('submit', '')) or \
                                     (tag == 'button' and btn_type in ('submit', ''))
                    if is_form_submit:
                        form = driver.execute_script("return arguments[0].form;", el)
                        if form:
                            driver.execute_script("arguments[0].scrollIntoView({behavior:'instant', block:'center'});", el)
                            time.sleep(0.3)
                            driver.execute_script("arguments[0].requestSubmit();", form)
                        try:
                            WebDriverWait(driver, 5).until(
                                lambda d: d.current_url != url_before)
                        except:
                            pass
                        url_after = driver.current_url
                        if url_before == url_after:
                            print(f"    ⚠️ clicar: URL não mudou ({url_before[:60]})")
                        else:
                            print(f"    ✅ clicar: redirect {url_before} → {url_after[:60]}")
                        continue

                # Links, checkboxes, etc: scroll + JS click
                driver.execute_script("arguments[0].scrollIntoView({behavior:'instant', block:'center'});", el)
                time.sleep(0.3)
                driver.execute_script("arguments[0].click();", el)
                time.sleep(1)

            elif acao == "scroll":
                driver.execute_script("""
                    (function() {
                        var alvo = arguments[0] || 300;
                        var passos = 10;
                        var inc = alvo / passos;
                        var i = 0;
                        function passo() {
                            if (i >= passos) return;
                            window.scrollBy({top: inc, behavior:'instant'});
                            i++;
                            setTimeout(passo, 200);
                        }
                        passo();
                    })();
                """, passo.get("pixels", 300))
                time.sleep(2.5)

            elif acao == "submit_modal":
                clicar_submit_modal(driver, passo["modal"])

            elif acao == "esperar":
                esperar_elemento(driver, passo["seletor"], passo.get("tempo", 10))

            elif acao == "shell":
                subprocess.run(passo["comando"], shell=True, check=False)
                time.sleep(1)

            elif acao == "legenda":
                pass

            # Verifica erro Laravel após cada ação (exceto shell, q é local)
            if acao != "shell":
                verificar_erro_laravel(driver)

            screenshot = screenshots_dir / f"passo_{passo_num:03d}.png"
            driver.save_screenshot(str(screenshot))
            print(f"         📸 screenshot: {screenshot.name}")

            time.sleep(passo.get("pausa", 1))

        print("\n✅ Roteiro concluído!")
    except Exception as e:
        print(f"\n❌ Erro no passo {passo_num}: {e}")
        import traceback
        traceback.print_exc()
        raise


# ── Selenium ──────────────────────────────────────────────────────────────────

def abrir_navegador():
    from selenium import webdriver
    from selenium.webdriver.chrome.options import Options
    from selenium.webdriver.chrome.service import Service

    # Mata apenas instâncias Chromium do Selenium (nunca Google Chrome)
    subprocess.run(["killall", "-9", "chromium", "chromium-browser"],
                   stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    time.sleep(1)

    # Remove session state do perfil isolado (evita cache que quebra forms)
    profile_dir = Path("/tmp/selenium-chrome-profile")
    if profile_dir.exists():
        import shutil
        shutil.rmtree(str(profile_dir))
        print("  [chrome] Perfil limpo (rmtree)")
        time.sleep(0.5)

    opts = Options()
    # Perfil isolado para não afetar o Chrome do usuário
    opts.add_argument("--user-data-dir=/tmp/selenium-chrome-profile")
    opts.add_argument("--kiosk")
    opts.add_argument("--no-sandbox")
    opts.add_argument("--disable-dev-shm-usage")
    opts.add_argument("--disable-gpu")
    opts.add_argument("--disable-save-password-bubble")
    opts.add_argument("--disable-password-generation")
    opts.add_argument("--disable-password-manager-reauthentication")
    opts.add_argument("--disable-password-protection-service")
    opts.add_argument("--disable-autofill-keyboard-accessory-view")
    opts.add_argument("--disable-sync")
    opts.add_argument("--disable-features=PasswordProtectionForFederatedLogins,LeakDetection,PasswordLeakDetection,PasswordCheck,PasswordManager,ChromePasswordManager,ChromePasswordManager2,PasswordStrengthIndicator,ParsingPassword,BufferPassword,PasswordImport,PasswordExport,PasswordManagerRedesign,PasswordManagerOnboarding,PasswordProtectionForAccountEmails,SafetyCheck,SafetyHub,SafetyCheckChild")
    opts.add_argument("--password-store=basic")
    opts.add_argument("--disable-prompt-on-repost")
    opts.add_argument("--allow-running-insecure-content")
    opts.add_argument("--unsafely-treat-insecure-origin-as-secure=http://127.0.0.1:8000")
    opts.add_argument("--disable-blink-features=AutomationControlled")
    opts.add_argument("--disable-client-side-phishing-detection")
    opts.add_argument("--incognito")
    opts.add_argument("--disable-sync-password")

    # Basic password store — essencial para não exibir mensagem de senha vazada
    os.environ["CHROME_PASSWORD_STORE"] = "basic"
    prefs = {
        "credentials_enable_service": False,
        "profile.password_manager_enabled": False,
        "profile.default_content_setting_values.notifications": 2,
        "autofill.profile_enabled": False,
        "autofill.credit_card_enabled": False,
        "profile.password_manager_leak_detection": False,
        "safebrowsing.enabled": False,
        "safebrowsing.password_protection_show_dom_component": False,
        "password_manager_leak_detection": False,
        "profile.content_settings.exceptions.autofill": {},
        "profile.password_model": False,
        "profile.password_sharing_enabled": False,
    }
    opts.add_experimental_option("prefs", prefs)

    chromedriver_path = str(Path.cwd() / "vendor/laravel/dusk/bin/chromedriver-linux")
    service = Service(executable_path=chromedriver_path)

    driver = webdriver.Chrome(service=service, options=opts)
    driver.implicitly_wait(5)
    return driver


# ── Main ──────────────────────────────────────────────────────────────────────

def main():
    global modulo_info

    parser = argparse.ArgumentParser(description="Motor de vídeos de treinamento VetEssence")
    parser.add_argument("--modulo", help="Nome do módulo (ex: 11-tutores-pets)")
    parser.add_argument("--list", action="store_true", help="Listar módulos disponíveis")
    args = parser.parse_args()

    if args.list:
        listar_modulos()
        return

    if not args.modulo:
        print("Use --modulo ou --list")
        sys.exit(1)

    if args.modulo not in CATALOGO:
        print(f"Erro: módulo '{args.modulo}' não encontrado.")
        listar_modulos()
        sys.exit(1)

    modulo_info = CATALOGO[args.modulo]
    print(f"🎬 Treinamento Automatizado — {modulo_info['nome']}")
    print("=" * 50)

    print(f"\n[1/4] Abrindo navegador…")
    driver = abrir_navegador()
    driver.get("http://127.0.0.1:8000/login")
    time.sleep(2)

    print(f"\n[2/4] Iniciando gravação (ffmpeg x11grab)…")
    iniciar_gravacao()

    print(f"\n[3/4] Executando roteiro…")
    executar_roteiro(driver, modulo_info["passos"])

    print(f"\n[4/4] Finalizando…")
    driver.quit()
    parar_gravacao()

    if VIDEO_FILE and Path(VIDEO_FILE).exists():
        size = Path(VIDEO_FILE).stat().st_size
        print(f"\n📁 Vídeo salvo em: {VIDEO_FILE} ({size // 1024} KB)")
    else:
        print("\n❌ Vídeo não foi salvo!")

    print("Concluído!")


if __name__ == "__main__":
    main()

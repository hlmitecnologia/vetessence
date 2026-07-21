# ── Roteiros de Treinamento ──────────────────────────────────────────────────
# Cada roteiro define os passos para um módulo da documentação.
# Formato dos passos:
#   {"passo": N, "acao": str, "legenda": str, "pausa": N, ...}
#
# Ações suportadas:
#   navegar    → {"url": "..."}
#   preencher  → {"seletor": "css", "valor": "..."}
#   livewire   → {"wire_model": "...", "valor": "..."}
#   clicar     → {"seletor": "css"}
#   esperar    → {"seletor": "css"}  (espera elemento aparecer)
#   legenda    → {"texto": "..."}  (exibe no console, sem ação no browser)
#   submit_modal → {"modal": "#tutorModal"}  (clica submit dentro do modal)
#   tom_select → {"wire_model": "...", "valor": "...", "label": "..."}
BASE_URL = "http://127.0.0.1:8000"

# ── Helpers para criar passos comuns ─────────────────────────────────────────

def login(email, senha):
    return [
        {"passo": 1, "acao": "navegar", "url": f"{BASE_URL}/login",
         "legenda": "Acessando o sistema…", "pausa": 2},
        {"passo": 2, "acao": "preencher", "seletor": "input[name=email]", "valor": email,
         "legenda": "Digitando o email…", "pausa": 1},
        {"passo": 3, "acao": "preencher", "seletor": "input[name=password]", "valor": senha,
         "legenda": "Digitando a senha…", "pausa": 1},
        {"passo": 4, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Entrando no sistema…", "pausa": 3},
    ]

def logout():
    return [
        {"passo": 999, "acao": "navegar", "url": f"{BASE_URL}/logout",
         "legenda": "Saindo do sistema…", "pausa": 2},
    ]

# ── Roteiros ──────────────────────────────────────────────────────────────────

ROTEIRO_11_TUTORES_PETS = {
    "nome": "Tutores e Pets",
    "arquivo": "11-tutores-pets",
    "credenciais": {"email": "recep@vet.com", "senha": "recep123"},
    "passos": [
        *login("recep@vet.com", "recep123"),
        # ── Cadastro de tutor ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/tutors",
         "legenda": "Abrindo cadastro de tutores…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Tutor…", "pausa": 2},
        {"passo": 8, "acao": "esperar", "seletor": "#tutorModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 9, "acao": "livewire", "wire_model": "name", "valor": "Maria das Dores",
         "legenda": "Preenchendo nome…", "pausa": 1},
        {"passo": 10, "acao": "livewire", "wire_model": "cpf", "valor": "123.456.789-00",
         "legenda": "Preenchendo CPF…", "pausa": 1},
        {"passo": 11, "acao": "livewire", "wire_model": "email", "valor": "maria.dores@email.com",
         "legenda": "Preenchendo e-mail…", "pausa": 1},
        {"passo": 12, "acao": "livewire", "wire_model": "phone", "valor": "(11) 99999-8888",
         "legenda": "Preenchendo telefone…", "pausa": 1},
        {"passo": 13, "acao": "livewire", "wire_model": "zipcode", "valor": "01001-000",
         "legenda": "Preenchendo CEP…", "pausa": 2},
        {"passo": 14, "acao": "livewire", "wire_model": "address", "valor": "Rua das Flores, 123",
         "legenda": "Preenchendo endereço…", "pausa": 1},
        {"passo": 15, "acao": "livewire", "wire_model": "neighborhood", "valor": "Centro",
         "legenda": "Preenchendo bairro…", "pausa": 1},
        {"passo": 16, "acao": "submit_modal", "modal": "#tutorModal",
         "legenda": "Salvando tutor…", "pausa": 4},
        # ── Cadastro de pet (modal) ──
        {"passo": 17, "acao": "navegar", "url": f"{BASE_URL}/pets",
         "legenda": "Abrindo cadastro de pets…", "pausa": 2},
        {"passo": 18, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 19, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Pet…", "pausa": 2},
        {"passo": 20, "acao": "esperar", "seletor": "#petModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 21, "acao": "livewire", "wire_model": "name", "valor": "Rex",
         "legenda": "Preenchendo nome do pet…", "pausa": 1},
        {"passo": 22, "acao": "livewire", "wire_model": "species", "valor": "canine",
         "legenda": "Selecionando espécie…", "pausa": 2},
        {"passo": 23, "acao": "tom_select", "wire_model": "tutor_id",
         "valor": "Maria das Dores",
         "legenda": "Selecionando tutor…", "pausa": 2},
        {"passo": 24, "acao": "livewire", "wire_model": "gender", "valor": "male",
         "legenda": "Selecionando sexo…", "pausa": 1},
        {"passo": 25, "acao": "livewire", "wire_model": "size", "valor": "medium",
         "legenda": "Selecionando porte…", "pausa": 1},
        {"passo": 26, "acao": "livewire", "wire_model": "color", "valor": "Caramelo",
         "legenda": "Preenchendo cor…", "pausa": 1},
        {"passo": 27, "acao": "livewire", "wire_model": "birth_date", "valor": "2023-05-15",
         "legenda": "Preenchendo data de nascimento…", "pausa": 1},
        {"passo": 28, "acao": "submit_modal", "modal": "#petModal",
         "legenda": "Salvando pet…", "pausa": 5},
        # ── Final ──
        {"passo": 29, "acao": "navegar", "url": f"{BASE_URL}/pets",
         "legenda": "Visualizando lista de pets…", "pausa": 2},
        {"passo": 30, "acao": "legenda", "texto": "Tutor Maria das Dores e pet Rex cadastrados com sucesso!",
         "pausa": 4},
    ],
}

ROTEIRO_07_FARMACIA = {
    "nome": "Farmácia",
    "arquivo": "07-farmacia",
    "credenciais": {"email": "super@vet.com", "senha": "super123"},
    "passos": [
        *login("super@vet.com", "super123"),
        # ── Categoria Produto (admin) ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/categories",
         "legenda": "Abrindo categorias…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Nova Categoria…", "pausa": 2},
        {"passo": 8, "acao": "esperar", "seletor": "#categoryModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 9, "acao": "livewire", "wire_model": "name", "valor": "Medicamentos",
         "legenda": "Preenchendo nome da categoria…", "pausa": 1},
        {"passo": 10, "acao": "livewire", "wire_model": "type", "valor": "product",
         "legenda": "Selecionando tipo Produto…", "pausa": 1},
        {"passo": 11, "acao": "submit_modal", "modal": "#categoryModal",
         "legenda": "Salvando categoria…", "pausa": 4},
        # ── Fornecedor ──
        {"passo": 12, "acao": "navegar", "url": f"{BASE_URL}/suppliers",
         "legenda": "Abrindo fornecedores…", "pausa": 2},
        {"passo": 13, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 14, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Fornecedor…", "pausa": 2},
        {"passo": 15, "acao": "esperar", "seletor": "#supplierModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 16, "acao": "livewire", "wire_model": "name", "valor": "FarMed Distribuidora",
         "legenda": "Preenchendo nome do fornecedor…", "pausa": 1},
        {"passo": 17, "acao": "livewire", "wire_model": "phone", "valor": "(11) 3000-4000",
         "legenda": "Preenchendo telefone…", "pausa": 1},
        {"passo": 18, "acao": "livewire", "wire_model": "email", "valor": "contato@farmdist.com.br",
         "legenda": "Preenchendo e-mail…", "pausa": 1},
        {"passo": 19, "acao": "submit_modal", "modal": "#supplierModal",
         "legenda": "Salvando fornecedor…", "pausa": 4},
        # ── Produto ──
        {"passo": 20, "acao": "navegar", "url": f"{BASE_URL}/products",
         "legenda": "Abrindo produtos…", "pausa": 2},
        {"passo": 21, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 22, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Produto…", "pausa": 2},
        {"passo": 23, "acao": "esperar", "seletor": "#productModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 24, "acao": "livewire", "wire_model": "name", "valor": "Dipirona 500mg",
         "legenda": "Preenchendo nome do produto…", "pausa": 1},
        {"passo": 25, "acao": "livewire", "wire_model": "stock", "valor": "100",
         "legenda": "Preenchendo estoque inicial…", "pausa": 1},
        {"passo": 26, "acao": "livewire", "wire_model": "cost_price", "valor": "5,00",
         "legenda": "Preenchendo preço de custo…", "pausa": 1},
        {"passo": 27, "acao": "livewire", "wire_model": "sale_price", "valor": "15,00",
         "legenda": "Preenchendo preço de venda…", "pausa": 1},
        {"passo": 28, "acao": "tom_select", "wire_model": "category_id",
         "valor": "Medicamentos",
         "legenda": "Selecionando categoria…", "pausa": 2},
        {"passo": 29, "acao": "tom_select", "wire_model": "supplier_id",
         "valor": "FarMed Distribuidora",
         "legenda": "Selecionando fornecedor…", "pausa": 2},
        {"passo": 30, "acao": "submit_modal", "modal": "#productModal",
         "legenda": "Salvando produto…", "pausa": 5},
        # ── Ajuste de Estoque (name-based TomSelect) ──
        {"passo": 31, "acao": "navegar", "url": f"{BASE_URL}/stock/adjust",
         "legenda": "Abrindo ajuste de estoque…", "pausa": 2},
        {"passo": 32, "acao": "preencher", "seletor": "select[name=type]", "valor": "entry",
         "legenda": "Selecionando tipo Entrada…", "pausa": 1},
        {"passo": 33, "acao": "tom_select", "wire_model": "branch_id",
         "valor": "Matriz",
         "legenda": "Selecionando unidade…", "pausa": 2},
        {"passo": 34, "acao": "tom_select", "wire_model": "product_id",
         "valor": "Dipirona 500mg",
         "legenda": "Selecionando produto…", "pausa": 2},
        {"passo": 35, "acao": "preencher", "seletor": "input[name=quantity]", "valor": "100",
         "legenda": "Preenchendo quantidade…", "pausa": 1},
        {"passo": 36, "acao": "preencher", "seletor": "input[name=batch_number]", "valor": "LOTE-001",
         "legenda": "Preenchendo lote…", "pausa": 1},
        {"passo": 37, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Salvando ajuste de estoque…", "pausa": 4},
        # ── Formulário de Medicamentos ──
        {"passo": 38, "acao": "navegar", "url": f"{BASE_URL}/drug-formulary",
         "legenda": "Abrindo formulário de fármacos…", "pausa": 2},
        {"passo": 39, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 40, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Fármaco…", "pausa": 2},
        {"passo": 41, "acao": "esperar", "seletor": "#drugFormularyModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 42, "acao": "livewire", "wire_model": "drug", "valor": "Dipirona Sódica",
         "legenda": "Preenchendo nome do fármaco…", "pausa": 1},
        {"passo": 43, "acao": "livewire", "wire_model": "species", "valor": "canina",
         "legenda": "Selecionando espécie…", "pausa": 1},
        {"passo": 44, "acao": "livewire", "wire_model": "dosage_mg_kg", "valor": "25",
         "legenda": "Preenchendo dosagem…", "pausa": 1},
        {"passo": 45, "acao": "livewire", "wire_model": "route", "valor": "IV",
         "legenda": "Selecionando via IV…", "pausa": 1},
        {"passo": 46, "acao": "submit_modal", "modal": "#drugFormularyModal",
         "legenda": "Salvando fármaco…", "pausa": 5},
        # ── Final: mostra lista de produtos com estoque ──
        {"passo": 47, "acao": "navegar", "url": f"{BASE_URL}/products",
         "legenda": "Visualizando lista de produtos…", "pausa": 3},
        {"passo": 48, "acao": "legenda", "texto": "Categoria, fornecedor, produto e fármaco cadastrados com sucesso!",
         "pausa": 4},
        *logout(),
    ],
}

ROTEIRO_10_AGENDAMENTO = {
    "nome": "Agendamento",
    "arquivo": "10-agendamento",
    "credenciais": {"email": "recep@vet.com", "senha": "recep123"},
    "passos": [
        *login("recep@vet.com", "recep123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Agendamento (implementação futura)",
         "pausa": 3},
    ],
}

ROTEIRO_01_PRONTUARIOS = {
    "nome": "Prontuários",
    "arquivo": "01-prontuarios",
    "credenciais": {"email": "vet@vet.com", "senha": "vet123"},
    "passos": [
        *login("vet@vet.com", "vet123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Prontuários (implementação futura)",
         "pausa": 3},
    ],
}

ROTEIRO_09_FINANCEIRO = {
    "nome": "Financeiro",
    "arquivo": "09-financeiro",
    "credenciais": {"email": "vet@vet.com", "senha": "vet123"},
    "passos": [
        *login("vet@vet.com", "vet123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Financeiro (implementação futura)",
         "pausa": 3},
        *logout(),
        *login("financeiro@vet.com", "fin123"),
        {"passo": 6, "acao": "legenda", "texto": "Perfil Financeiro logado",
         "pausa": 3},
    ],
}

# ── Catálogo de roteiros ─────────────────────────────────────────────────────
CATALOGO = {
    "11-tutores-pets": ROTEIRO_11_TUTORES_PETS,
    "07-farmacia": ROTEIRO_07_FARMACIA,
    "10-agendamento": ROTEIRO_10_AGENDAMENTO,
    "01-prontuarios": ROTEIRO_01_PRONTUARIOS,
    "09-financeiro": ROTEIRO_09_FINANCEIRO,
}

def listar_modulos():
    print("Módulos disponíveis:")
    for chave, valor in sorted(CATALOGO.items()):
        print(f"  {chave} — {valor['nome']}")
    print(f"\nTotal: {len(CATALOGO)} módulos")

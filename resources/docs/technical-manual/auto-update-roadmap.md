# Plano Médio Prazo — Auto-Update / Licenciamento

## Problema

O auto-update atual usa token GitHub exposto no frontend, sem validação de licença.
O plano de curto prazo já corrigiu: token criptografado no DB, não exposto na view,
senha para aplicar, campo `license_key` e fallback para `.env`.

## Implementado ✅

### ✅ 2. Backup automático antes da atualização

**Status:** Feito (controller + comando Artisan).  
Antes do `git pull`, cria `storage/app/backups/pre-update-{date}.sql` via mysqldump.

### ✅ 4. Rate limit / Throttle

**Status:** Feito.  
`SystemUpdateController::apply()` usa `Cache::put()` com 30 min de bloqueio por usuário.  
Botão "Aplicar" desabilitado na view com contador regressivo.

### ✅ 5. Comando Artisan dedicado

**Status:** Feito.  
`php artisan system:update` — executa backup, down, git pull, migrate, up.
Opções: `--force` (skip prompt), `--no-backup`, `--branch=` (override).

## Pendências para implementação futura

### 1. Endpoint de licenciamento remoto

Criar um servidor de licenças (rota HTTPS pública) que valide:

- `license_key` + `domain` (ou `APP_URL`)
- Responda `{ "valid": true/false, "expires_at": "..." }`

No Laravel, antes do `git pull`, fazer requisição ao servidor:

```php
$response = Http::timeout(5)->post('https://licensing.vetessence.com/verify', [
    'license_key' => $licenseKey,
    'domain' => config('app.url'),
]);
```

Se inválida ou expirada, bloquear a atualização com mensagem clara.

### 3. Notificação ao cliente

- Disparar e-mail para o admin quando houver atualização disponível
- Registrar evento em `update_logs` com resultado da validação de licença

### 6. Considerações de segurança adicionais

- Usar `CURLOPT_SSL_VERIFYPEER` explícito nas chamadas à API GitHub
- Logar tentativas de aplicar sem licença válida (alerta de segurança)
- Limitar acesso ao endpoint de licenciamento por IP (whitelist dos servidores dos clientes)

## Arquivos relevantes

- `app/Http/Controllers/SystemUpdateController.php`
- `config/update.php`
- `app/Models/Setting.php`
- `resources/views/system-update/index.blade.php`

# Plano Médio Prazo — Auto-Update / Licenciamento

## Problema

O auto-update atual usa token GitHub exposto no frontend, sem validação de licença.
O plano de curto prazo já corrigiu: token criptografado no DB, não exposto na view,
senha para aplicar, campo `license_key` e fallback para `.env`.

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

### 2. Backup automático antes da atualização

Antes de `git pull`, criar:

- **Dump do banco**: `exec("mysqldump ... > storage/backups/pre-update-{date}.sql")`
- **Snapshot dos arquivos**: `exec("git stash create")` ou `tar -czf storage/backups/files-{date}.tar.gz .`

Se o `git pull` falhar (merge conflict), restaurar automaticamente.

### 3. Notificação ao cliente

- Disparar e-mail para o admin quando houver atualização disponível
- Registrar evento em `update_logs` com resultado da validação de licença

### 4. Rate limit / Throttle

- Limitar tentativas de `apply()` a 1 vez a cada 30 minutos
- Evitar que múltiplos admins apliquem simultaneamente

### 5. Comando Artisan dedicado

Criar `php artisan system:update` que executa todo o fluxo sem depender
da interface web (útil para automação e recuperação de emergência).

### 6. Considerações de segurança adicionais

- Usar `CURLOPT_SSL_VERIFYPEER` explícito nas chamadas à API GitHub
- Logar tentativas de aplicar sem licença válida (alerta de segurança)
- Limitar acesso ao endpoint de licenciamento por IP (whitelist dos servidores dos clientes)

## Arquivos relevantes

- `app/Http/Controllers/SystemUpdateController.php`
- `config/update.php`
- `app/Models/Setting.php`
- `resources/views/system-update/index.blade.php`

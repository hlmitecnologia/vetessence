# Manual Técnico

Documentação para desenvolvedores e administradores do sistema.

---

## Arquitetura

### Stack
| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 8, PHP 7.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 2, FullCalendar 6, Chart.js |
| Banco | MySQL |
| Autenticação | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |

### Estrutura de Diretórios
```
app/
├─ Console/Commands/     # Comandos Artisan
├─ Events/               # Eventos do sistema
├─ Exceptions/           # Exceções customizadas
├─ Http/
│  ├─ Controllers/      # Controladores
│  │  └─ Portal/        # Portal do tutor
│  ├─ Livewire/         # Componentes Livewire
│  └─ Middleware/       # Middlewares
├─ Listeners/           # Listeners de eventos
├─ Models/              # Eloquent Models
├─ Providers/           # Service Providers
└─ Services/            # Classes de serviço
resources/
├─ views/
│  ├─ layouts/          # Layouts (adminlte, sidebar, mobile)
│  ├─ portal/           # Views do portal do tutor
│  └─ ...               # Views por módulo
routes/
├─ web.php              # Rotas principais
├─ portal.php           # Rotas do portal do tutor
├─ api.php              # Rotas de API
└─ console.php          # Rotas de console
```

### Escopo de Dados
- **Tutores e Pets**: Globais (compartilhados entre filiais)
- **Dados Operacionais**: Escopados por filial (branch_id)
- **Usuários**: Possuem branch_id (null = global)

---

## Módulos

### Fases Implementadas

| Fase | Descrição | Status |
|------|-----------|--------|
| A-G | Infraestrutura (schema, roles, middleware) | ✅ |
| H-K | RH (departamentos, cargos, funcionários, escalas) | ✅ |
| L-N | Clínico (prontuários, prescrições, vacinas, exames) | ✅ |
| O-P | Farmácia (produtos, estoque, lotes, substâncias controladas) | ✅ |
| Q | Gaps reais (aprovação, comissões, auto-invoice, Rx verification) | ✅ |
| R | Enhancement (Livewire triage, CVI PDF, auto-claim, QR Rx) | ✅ |
| S | Workflow diário (calendário, dashboard, chat, mobile, ordens de compra) | ✅ |
| T | Cobertura 100% (timeline, dosage calculator, portal tutor, price tiers, emergency protocols, corporate dashboard) | ✅ |
| U | Manutenção (auto-update, rebranding, docs, white label) | ✅ |

---

## Permissões

O sistema utiliza **Spatie Laravel Permission** com 10 papéis:

| Papel | Descrição |
|-------|-----------|
| super-admin | Acesso total |
| branch-admin | Administração por filial |
| veterinarian | Acesso clínico |
| receptionist | Agenda e cadastro |
| financial | Financeiro |
| super-financial | Financeiro global |
| stock-manager | Estoque |
| human-resources | RH |
| tutor | Portal do tutor |
| auditor | Apenas leitura |

As permissões seguem o padrão `modulo.acao` (ex: `appointments.create`, `products.view`).

---

## Integrações

### Comunicação

#### WhatsApp (Z-API)
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `communication.whatsapp.url` | `WHATSAPP_API_URL` | `https://api.z-api.io/v1` | URL base da API Z-API |
| `communication.whatsapp.token` | `WHATSAPP_API_TOKEN` | — | Token de autenticação Bearer |
| `communication.whatsapp.instance` | `WHATSAPP_INSTANCE` | — | ID da instância Z-API |

**Provider**: `App\Services\Communication\WhatsAppProvider`
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `whatsapp`

#### SMS
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `communication.sms.url` | `SMS_API_URL` | `https://api.smsprovider.com/v1/send` | URL base da API de SMS |
| `communication.sms.key` | `SMS_API_KEY` | — | Chave de API (Bearer token) |

**Provider**: `App\Services\Communication\SmsProvider`
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `sms`

#### E-mail API
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `email-api.url` | `EMAIL_API_URL` | `https://api.example.com/send` | URL base da API de e-mail |
| `email-api.token` | `EMAIL_API_TOKEN` | — | Token de autenticação |
| `email-api.timeout` | `EMAIL_API_TIMEOUT` | `15` | Timeout em segundos |

**Service**: `App\Services\EmailApiService`
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `email`

#### SMTP (Laravel Mail)
Variáveis padrão do Laravel: `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`.
Suporte a Mailgun, SES, Postmark via `config/services.php`.

---

### Pagamentos

#### PIX
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `pix.pix_key` | `PIX_KEY` | `admin@vetessence.com` | Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória) |
| `pix.gi` | `PIX_GI` | `br.gov.bcb.pix` | GUI (identificador do arranjo de pagamentos) |
| `pix.merchant_name` | `PIX_MERCHANT_NAME` | `VETESSENCE CLINICA VETERINARIA` | Nome do recebedor (ate 25 caracteres) |
| `pix.city` | `PIX_CITY` | `SAO PAULO` | Cidade do recebedor |
| `pix.url` | `PIX_URL` | — | URL opcional para payload dinâmico |

**Service**: `App\Services\PixService` — gera payload EMV + QR Code
**Testes**: `tests/Unit/Services/PixServiceTest.php` (9 testes)

#### Gateway de Pagamento
Gerenciado via banco de dados na tabela `payment_gateways`. Acesse o painel admin para configurar:

| Campo | Descrição |
|-------|-----------|
| `provider` | Nome do provedor (mercadopago, pagseguro, stripe, pix) |
| `public_key` | Chave pública/API |
| `secret_key` | Chave secreta (não serializada) |
| `webhook_secret` | Segredo para validar callbacks |
| `webhook_url` | URL de callback |
| `is_sandbox` | Modo de teste |

> **Nota**: O CRUD de gateways está implementado, mas a chamada real à SDK do provedor (`PaymentService@charge`) ainda é um stub.

---

### APIs Externas

#### GitHub (Auto-Update)
Configurado via painel admin em **Configurações > Atualizar Sistema**:

| Chave (settings table) | Descrição |
|------------------------|-----------|
| `github_token` | Token de acesso pessoal GitHub |
| `github_repo` | Repositório (ex: `hectordufau/vetessence`) |
| `github_branch` | Branch (ex: `main`) |

**Controller**: `App\Http\Controllers\SystemUpdateController`
**Fluxo**: `php artisan down` → `git pull https://token@github.com/...` → `php artisan migrate` → limpa cache → `php artisan up`

#### Porto Seguro (Insurance Claims)
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `insurance.porto_seguro.url` | `PORTO_SEGURO_API_URL` | `https://api.portoseguro.com.br/v1/claims` | URL base da API |
| `insurance.porto_seguro.key` | `PORTO_SEGURO_API_KEY` | — | Chave de API |
| `insurance.porto_seguro.timeout` | `PORTO_SEGURO_TIMEOUT` | `30` | Timeout em segundos |

**Provider**: `App\Services\Insurance\PortoSeguroProvider`
**Comando**: `claims:auto-file` — envia claims pendentes automaticamente
**Webhook**: `POST /api/insurance/webhook` (público, rate-limited: 60/min)

#### Equipamentos de Laboratório
Gerenciado via banco de dados na tabela `lab_equipment_integrations`. Acesse o painel admin:

| Campo | Descrição |
|-------|-----------|
| `equipment_type` | Tipo de equipamento |
| `protocol` | Protocolo (rest, hl7, fhir, custom) |
| `endpoint_url` | URL HTTP para envio/recepção |
| `api_key` | Chave de API |
| `ip_address` | Endereço IP (para HL7 direto) |
| `port` | Porta TCP |

**Endpoints públicos**: `POST /api/v1/lab-equipment/{id}/receive` e `GET /api/v1/lab-equipment/{id}/status`

#### Jitsi Meet (Teleconsulta)
Gera salas automaticamente no formato `https://meet.jit.si/{app-name}-{token}`. Nenhuma configuração adicional necessária.

---

## Variáveis de Ambiente

| Variável | Descrição |
|----------|-----------|
| `APP_NAME` | Nome do sistema |
| `DB_HOST` | Host do banco |
| `DB_PORT` | Porta do banco |
| `DB_DATABASE` | Nome do banco |
| `DB_USERNAME` | Usuário do banco |
| `DB_PASSWORD` | Senha do banco |
| `WHATSAPP_API_URL` | URL da API WhatsApp (Z-API) |
| `WHATSAPP_API_TOKEN` | Token da API WhatsApp |
| `WHATSAPP_INSTANCE` | Instância Z-API |
| `SMS_API_URL` | URL da API de SMS |
| `SMS_API_KEY` | Chave da API de SMS |
| `EMAIL_API_URL` | URL da API de e-mail |
| `EMAIL_API_TOKEN` | Token da API de e-mail |
| `PORTO_SEGURO_API_URL` | URL da API Porto Seguro |
| `PORTO_SEGURO_API_KEY` | Chave da API Porto Seguro |
| `GITHUB_TOKEN` | Token para auto-update |
| `PIX_KEY` | Chave PIX |
| `SESSION_DRIVER` | Driver de sessão (file, database, redis) |
| `QUEUE_CONNECTION` | Driver de fila (sync, database, redis) |

---

## Deploy

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- Composer
- Node.js (para assets)
- Git

### Passos
```bash
git clone https://github.com/hectordufau/vetessence.git
cd vetessence
cp .env.example .env
composer install --no-dev
npm install && npm run production
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### Manutenção
```bash
php artisan down  # Modo manutenção
php artisan up    # Reativar
```

### Auto-Update
Admin pode atualizar via painel em **Configurações > Atualizar Sistema**.
Requer token GitHub configurado.

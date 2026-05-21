# Manual Técnico

Documentação para desenvolvedores e administradores do sistema.

---

## Arquitetura

### Stack
| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 3, FullCalendar 6, Chart.js, TomSelect 2.3 |
| Banco | MySQL 8+ |
| Autenticação | Laravel Breeze, Spatie Permissions v7 |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |
| Markdown | league/commonmark |

### Estrutura de Diretórios
```
app/
├─ Console/Commands/     # Comandos Artisan (~15 comandos)
├─ Events/               # Eventos do sistema
├─ Exceptions/           # Exceções customizadas
├─ Http/
│  ├─ Controllers/      # Controladores (~50)
│  │  └─ Portal/        # Portal do tutor (~8 controllers)
│  ├─ Livewire/         # Componentes Livewire (~35)
│  └─ Middleware/       # Middlewares (SetBranchContext, etc.)
├─ Listeners/           # Listeners de eventos
├─ Models/              # Eloquent Models (~60)
├─ Providers/           # Service Providers
└─ Services/            # Classes de serviço
    ├─ Communication/   # WhatsAppProvider, SmsProvider
    ├─ Insurance/       # PortoSeguroProvider
    └─ Nfse/            # NfseProvider, NfseService, WebmaniaProvider
resources/
├─ docs/                # Documentação em Markdown (source)
├─ views/
│  ├─ layouts/          # Layouts (adminlte, sidebar, mobile)
│  ├─ livewire/         # Views dos componentes Livewire
│  ├─ portal/           # Views do portal do tutor
│  └─ ...               # Views por módulo
routes/
├─ web.php              # Rotas principais
├─ portal.php           # Rotas do portal do tutor
├─ api.php              # Rotas de API
└─ console.php          # Rotas de console
storage/
└─ docs/                # Documentação publicada (cópia de resources/docs/)
tests/
├─ Unit/Models/         # Testes unitários de modelos (~290)
├─ Feature/Controllers/ # Testes de controllers (~400)
├─ Feature/Commands/    # Testes de comandos (~25)
├─ Feature/Integrations/# Testes de fluxo (~12)
├─ Feature/Api/         # Testes de API (~18)
├─ Feature/Portal/      # Testes do portal (~20)
├─ Feature/Services/    # Testes de serviços (~12)
└─ Unit/Services/       # Testes unitários de serviços (~18)
```

### Escopo de Dados
- **Tutores e Pets**: Globais (compartilhados entre filiais)
- **Dados Operacionais**: Escopados por filial (branch_id)
- **Usuários**: Possuem branch_id (null = global)
- **Convênios, Fornecedores, Produtos**: Globais
- **Financeiro, Estoque, Agendamentos**: Escopados por filial

### Requisitos de Hardware (Demonstração)

| Componente | Mínimo | Recomendado |
|---|---|---|
| **CPU** | 1 core (x86_64) | 2 cores |
| **RAM** | 2 GB | 4 GB |
| **Armazenamento** | 10 GB SSD | 20 GB SSD |
| **SO** | Ubuntu 22.04+ / Debian 12+ | Ubuntu 24.04 LTS |

**Stack:** PHP 8.2+ (extensões: bcmath, ctype, fileinfo, gd, intl, json, mbstring, openssl, PDO, pdo_mysql, tokenizer, xml, zip, curl, libxml), Nginx, MySQL 8+ / MariaDB 10.6+, Redis (opcional em demo). Node.js 18+ apenas para build de assets.

**Consumo estimado** (1–5 usuários simultâneos): ~1 GB RAM, ~565 MB de disco.

### CRUD Pattern (Phase V — Modal CRUD)
CRUDs de Tier 1 e Tier 2 usam modais Bootstrap + Livewire form components:
- `app/Livewire/{Entity}Form.php` — Livewire component com mount($id), validação, save()
- `resources/views/livewire/{entity}-form.blade.php` — form sem layout
- Delete via SweetAlert2 global interceptador de `form[method=DELETE]`
- ~29 Livewire form components, 27 index views com modal

---

## Módulos

### Fases Implementadas
| Fase | Descrição | Status |
|------|-----------|--------|
| A-G | Infraestrutura (schema, roles, middleware) | ✅ |
| H-K | RH (departamentos, cargos, funcionários, escalas) | ✅ |
| L-N | Clínico (prontuários, prescrições, vacinas, exames) | ✅ |
| O-P | Farmácia (produtos, estoque, lotes, substâncias controladas) | ✅ |
| P   | Features (eutanásia, pré-anestesia, dietas, claims, CVI, triagem) | ✅ |
| Q   | Gaps reais (lote, aprovação, microchip, auto-invoice, comissões, Rx verification, conciliação) | ✅ |
| R   | Enhancement (Livewire triage, CVI PDF, auto-claim, QR Rx) | ✅ |
| S   | Workflow diário (calendário, dashboard, chat, mobile, ordens de compra) | ✅ |
| T   | Cobertura 100% (timeline, dosage calculator, portal tutor, price tiers, emergency protocols, corporate dashboard) | ✅ |
| U   | Manutenção (auto-update, rebranding, docs, white label) | ✅ |
| V   | Modal CRUD + SweetAlert2 (29 Livewire form components) | ✅ |
| W   | NFSe (Nota Fiscal de Serviços Eletrônica) | ✅ |

### Módulos do Sistema (29 módulos no Manual do Usuário)
| # | Módulo | Descrição |
|---|--------|-----------|
| 5  | Prontuários | SOAP, planos de tratamento, aprovação, dietas, consentimento |
| 6  | Prescrições | Receita digital, dosagem, verificação QR code, impressão |
| 7  | Vacinas | Aplicação, protocolos, certificado PDF, lembretes, previsão, recall |
| 8  | Exames | Solicitação, coleta, resultado, laudo |
| 9  | Laboratório | Pedidos, amostras, parâmetros, equipamentos integrados |
| 10 | Imagem | Raio-X, ultrassom, tomografia, laudos |
| 11 | Cirurgias | Agendamento, checklist, anestesia, transoperatório |
| 12 | Internações | Registro, evolução, prescrição diária, alta |
| 13 | Farmácia | Produtos, categorias, fornecedores, calculadora dosagem, lotes |
| 14 | Estoque | Movimentações, pedidos compra, substâncias controladas, scanner |
| 15 | Financeiro | Faturas, recebimentos, NFSe, comissões, conciliação bancária |
| 16 | Agendamento | Calendário visual, agendamento online, recorrente, lembretes |
| 17 | Tutores e Pets | Cadastro, microchip/RG, timeline, óbito, portal |
| 18 | Convênios | Cadastro, tabelas, guias, faturamento, claims, CVI |
| 19 | Usuários e Permissões | 11 funções, 160+ permissões |
| 20 | Multi-filiais | Estrutura, corporate dashboard, transferências |
| 21 | Relatórios | Clínicos, financeiros, estoque, exportação |
| 22 | Auditoria e LGPD | Trilha auditoria, direitos titular, anonimização |
| 23 | Notificações | Canais, preferências tutor, campanhas |
| 24 | Chat | Mensagens tutor ↔ clínica, anexos |
| 25 | Configurações | Sistema, integrações, identidade visual, auto-update |
| 26 | Emergências | Protocolos de emergência por espécie/gravidade |
| 27 | Mobile | Interface responsiva, modo mobile /m |
| 28 | Triagem | Painel Livewire, classificação Manchester, tempo real |
| 29 | Hospedagem | Boarding, check-in/out, tarefas, banho e tosa |
| 30 | Odontologia | Odontograma, procedimentos, periodontia |
| 31 | Zoonoses | Cadastro, notificação compulsória, relatórios |

---

## Permissões

O sistema utiliza **Spatie Laravel Permission v7** com **11 papéis**:

| Papel | Slug | Descrição | Permissões |
|-------|------|-----------|------------|
| Super Admin | super-admin | Acesso total irrestrito | ~160 (todas) |
| Admin | admin | Acesso total ao sistema | ~160 (todas) |
| Branch Admin | branch-admin | Administração por filial | ~160 (escopo filial) |
| Veterinarian | veterinarian | Acesso clínico completo | ~105 |
| Receptionist | receptionist | Agenda e cadastro | ~22 |
| Financial | financial | Módulo financeiro | ~14 |
| Super Financial | super-financial | Financeiro global | ~19 |
| Stock Manager | stock-manager | Estoque e farmácia | ~25 |
| Human Resources | human-resources | RH | ~10 |
| Tutor | tutor | Portal do tutor | 0 |
| Auditor | auditor | Apenas leitura | ~80+ |

As permissões seguem o padrão `modulo.acao` (ex: `appointments.create`, `products.view`, `nfse.emit`).

### Categorias de Permissão
- **Admin:** admin.view, users.*, roles.*, branches.*
- **Cadastro:** tutors.*, pets.*, convenios.*
- **Clínico:** medical-records.*, prescriptions.*, vaccinations.*, exams.*, surgeries.*, triage.*
- **Farmácia:** products.*, stock.*, suppliers.*, categories.*, controlled-substances.*
- **Financeiro:** invoices.*, payments.*, nfse.*, commissions.*, bank-reconciliation.*
- **Estoque:** purchase-orders.*, stock.transfer
- **Sistema:** configuracoes.view, docs.view, system-update, branding
- **Outros:** chat.*, emergency-protocols.*, diet-plans.*, pre-anesthetic.*, convenio-claims.*

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

### NFSe (Webmania®)

**Provider**: `App\Services\Nfse\WebmaniaProvider` (implementa `NfseProvider` interface)
**Adapter**: `App\Services\Nfse\NfseService` — orquestra configuração → payload → emissão → persistência
**Endpoints**: `POST /v1/nfse/emitir`, `GET /v1/nfse/{id}`, `POST /v1/nfse/{id}/cancelar`
**Autenticação**: Headers `X-App-Id`, `X-App-Secret`, `X-Consumer-Key`, `X-Consumer-Secret`
**Arquitetura**: Adapter Pattern — permite trocar Webmania® por outro provedor sem alterar regras de negócio

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

#### NFSe Webhook
**Endpoint**: `POST /api/webhooks/nfse/{branch_id}` (público, rate-limited: 60/min)
**Uso**: Recebe callbacks da Webmania® com atualização de status de NFSe (opcional, processamento síncrono alternativo)

---

## Webhooks

| Endpoint | Público | Rate Limit | Descrição |
|----------|---------|------------|-----------|
| `POST /r/{hash}` | Sim | 10 req/min | Verificação pública de prescrição |
| `POST /api/insurance/webhook` | Sim | 60/min | Callback de status de claims |
| `POST /api/webhooks/nfse/{branch_id}` | Sim | 60/min | Callback de atualização NFSe |
| `POST /api/v1/lab-equipment/{id}/receive` | Sim | — | Recebimento de resultados lab |
| `GET /api/v1/lab-equipment/{id}/status` | Sim | — | Status de equipamento lab |

---

## Testes

### Suite Completa

| Suite | Count | Descrição |
|-------|-------|-----------|
| Unit/Models | ~290 | Todos os modelos |
| Feature/Controllers | ~400 | Todos os controllers |
| Feature/Commands | ~25 | Comandos Artisan |
| Feature/Integrations | ~12 | Fluxos completos |
| Feature/Api | ~18 | Endpoints de API |
| Feature/Portal | ~20 | Portal do tutor |
| Feature/Services | ~12 | Serviços (NFSe providers, etc.) |
| Unit/Services | ~18 | Serviços (Pix, EmailApi, BranchContext) |
| **Total** | **~887** | **238 files, 865 methods, 1520 assertions** |

### Como Rodar

```bash
# Todos os testes
php artisan test

# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Filter por controller
php artisan test --filter="DepartmentController|NfseController"

# Verboso
php artisan test --filter="PetControllerTest::test_index" --verbose
```

### Database
- Driver: `mysql_testing` (configurado em `config/database.php`)
- Host: `127.0.0.1:3307`, Database: `vetessence_testing`
- Todas as migrations aplicadas
- `DatabaseTransactions` (não `RefreshDatabase`)

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
| `PIX_MERCHANT_NAME` | Nome do recebedor PIX |
| `PIX_CITY` | Cidade do recebedor PIX |
| `WEBMANIA_APP_ID` | App ID Webmania NFSe |
| `WEBMANIA_APP_SECRET` | App Secret Webmania NFSe |
| `WEBMANIA_CONSUMER_KEY` | Consumer Key Webmania NFSe |
| `WEBMANIA_CONSUMER_SECRET` | Consumer Secret Webmania NFSe |
| `SESSION_DRIVER` | Driver de sessão (file, database, redis) |
| `QUEUE_CONNECTION` | Driver de fila (sync, database, redis) |

---

## Deploy

### Pré-requisitos
- PHP 8.4+
- MySQL 8+
- Composer
- Node.js (para assets)
- Git
- Extensões PHP: bcmath, ctype, json, mbstring, openssl, PDO, tokenizer, xml, gd, zip

### Passos
```bash
git clone https://github.com/hectordufau/vetessence.git
cd vetessence
cp .env.example .env
composer install --no-dev
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan docs:publish
```

### Manutenção
```bash
php artisan down  # Modo manutenção
php artisan up    # Reativar
```

### Auto-Update
Admin pode atualizar via painel em **Configurações > Atualizar Sistema**.
Requer token GitHub configurado e `exec()` habilitado no servidor.

---

## Documentação do Sistema

- **Source**: `resources/docs/` (arquivos .md)
- **Published**: `storage/docs/` (via comando `docs:publish`)
- **Admin route**: `/docs` (requer permissão `docs.view`)
- **Tutor route**: `/portal/docs` (autenticado como tutor)
- **Controllers**: `DocController` + `Portal\DocController`
- **Conteúdo**:
  - Manual do Usuário: 29 módulos (05 a 31)
  - Manual Técnico: Este documento
  - Changelog
  - Manual do Tutor: 13 tópicos

---

## Diagrama do Processo

![Matriz de Perfis RACI](../diagrams/matriz-perfis.svg)
*Clique na imagem para ampliar. Diagrama BPMN 2.0 — setas contínuas = fluxo sequencial, tracejadas = fluxo de mensagem, losangos = decisão.*

---

## Diagrama do Processo

![Recursos Humanos](../diagrams/rh-fluxo-admissao.svg)
*Clique na imagem para ampliar. Diagrama BPMN 2.0 — setas contínuas = fluxo sequencial, tracejadas = fluxo de mensagem, losangos = decisão.*

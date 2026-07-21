# Manual Técnico

Documentação para desenvolvedores e administradores do sistema.

---

## Arquitetura

### Stack
| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 3, FullCalendar 6, Chart.js, TomSelect, html5-qrcode |
| Banco | MySQL 8+ |
| Autenticação | Laravel Breeze, Spatie Permissions v7 |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |
| Markdown | league/commonmark |

### Estrutura de Diretórios
```
app/
  ├─ Console/Commands/     # Comandos Artisan (~18 comandos)
├─ Events/               # Eventos do sistema
├─ Exceptions/           # Exceções customizadas
├─ Http/
│  ├─ Controllers/      # Controladores (~68)
│  │  └─ Portal/        # Portal do tutor (~12 controllers)
│  ├─ Livewire/         # Componentes Livewire (~42)
│  └─ Middleware/       # Middlewares (SetBranchContext, etc.)
├─ Listeners/           # Listeners de eventos
├─ Models/              # Eloquent Models (~66: +PetShopPackage, PetShopSubscription, PetShopConsumption)
├─ Providers/           # Service Providers
    └─ Services/            # Classes de serviço (StockForecastService, VetAvailabilityService, etc.)
    ├─ Communication/   # WhatsAppProvider, SmsProvider
    ├─ Insurance/       # PortoSeguroProvider, PetloveProvider
    ├─ Nfe/             # NfeProvider, NfeService, FocusNfeProvider, NfeIoProvider, WebmaniaProvider
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
├─ Feature/Controllers/ # Testes de controllers (~405)
├─ Feature/Commands/    # Testes de comandos (~25)
├─ Feature/Integrations/# Testes de fluxo (~12)
├─ Feature/Api/         # Testes de API (~18)
├─ Feature/Portal/      # Testes do portal (~20)
├─ Feature/Services/    # Testes de serviços (~12)
└─ Unit/Services/       # Testes unitários de serviços (~30)
```

### Escopo de Dados
- **Tutores e Pets**: Globais (compartilhados entre filiais)
- **Dados Operacionais**: Escopados por filial (branch_id)
- **Usuários**: Possuem branch_id (null = global)
- **Convênios, Fornecedores, Produtos**: Globais
- **Financeiro, Estoque, Agendamentos**: Escopados por filial

### Requisitos de Hardware

#### Demonstração (1–5 usuários)

| Componente | Mínimo | Recomendado |
|---|---|---|
| **CPU** | 1 core | 2 cores |
| **RAM** | 2 GB | 4 GB |
| **Armazenamento** | 10 GB SSD | 20 GB SSD |

**Stack:** PHP 8.2+, Nginx, MySQL 8+ / MariaDB 10.6+, Redis opcional. Consumo ~1 GB RAM.

#### Produção (50–200 usuários)

Arquitetura com **2 servidores** separando aplicação e banco:

| Componente | Aplicação | Banco de Dados |
|---|---|---|
| **CPU** | 4 cores | 4–8 cores |
| **RAM** | 8 GB | 16 GB |
| **Armazenamento** | 50 GB SSD | 100 GB SSD |

**Stack obrigatória:** PHP-FPM (8–16 workers), Nginx, MySQL 8+ dedicado (`innodb_buffer_pool_size` 70–80% da RAM), Redis (filas + cache + sessões), Supervisor (2–4 workers `queue:work`), backups diários, fail2ban, Certbot TLS.

**Consumo (app):** ~2 GB RAM. **Consumo (banco):** ~10 GB RAM.

> Para alta disponibilidade: balanceador + 2+ servidores app + réplica MySQL.

### CRUD Pattern (Phase V — Modal CRUD + DataTables Client-Side)

**Modal CRUD:** CRUDs de Tier 1 e Tier 2 usam modais Bootstrap + Livewire form components:
- `app/Livewire/{Entity}Form.php` — Livewire component com mount($id), validação, save()
- `resources/views/livewire/{entity}-form.blade.php` — form sem layout
- Delete via SweetAlert2 global interceptador de `form[method=DELETE]`
- ~33 Livewire form components, 32 index views com modal

**DataTables Client-Side:** 48 controllers migraram de `paginate()` (server-side) para `get()` + `data-order` (client-side DataTables). Isso:
- Reduz consultas SQL (uma única query sem COUNT)
- Habilita ordenação por colunas sem recarregar página
- Simplifica os controllers (removem lógica de ordenação server-side)

### Service Type Maps (Pós-Fase W)
Mapeamento entre tipo de atendimento e serviço com preço para geração de faturas:

| Item | Detalhe |
|------|---------|
| Tabela | `service_type_maps` |
| Model | `app/Models/ServiceTypeMap.php` — relations: `service()`, `branch()` |
| Controller | `ServiceController@updateTypeMap` — método PUT |
| Rota | `PUT services/type-map/{type}` |
| Uso | `MedicalRecordController@generateInvoice` busca o mapeamento por tipo+branch e cria `InvoiceItem` com `unit_price = service.price` |
| Uso 2 | `GenerateInvoiceFromAppointment` listener usa `appointment.services` com fallback para `service_type_maps` |

**Controllers atualizados:**
- `InvoiceController` — novo método `cancel()` (altera status para `cancelled`); `pay()` agora dispara `InvoicePaid::dispatch()`
- `MedicalRecordController` — método `generateInvoice()` usa `ServiceTypeMap` para definir preço
- `ServiceController` — `index()` passa `medicalTypes`, `typeMaps`, `branchId`; `updateTypeMap()` cria/remove mapeamento

**Rotas adicionadas:**
- `POST invoices/{invoice}/cancel` — `invoices.cancel`
- `PUT services/type-map/{type}` — `services.type-map.update`
- `GET medical-records/{record}/generate-invoice` — `medical-records.generate-invoice`

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
| ZG  | Diferenciais Competitivos (estoque inteligente, pacotes petshop, Petlove) | ✅ |

### Módulos do Sistema (26 módulos no Manual do Usuário)
| # | Módulo | Descrição |
|---|--------|-----------|
| 01 | Prontuários | SOAP, planos de tratamento, aprovação, dietas, consentimento |
| 02 | Prescrições | Receita digital, dosagem, verificação QR code, impressão |
| 03 | Vacinas | Aplicação, protocolos, certificado PDF, lembretes, previsão, recall |
| 04 | Exames | Solicitação, coleta, resultado, laudo, laboratório, imagem |
| 05 | Cirurgias | Agendamento, checklist, anestesia, transoperatório |
| 06 | Internações | Registro, evolução, prescrição diária, alta |
| 07 | Farmácia | Produtos, categorias, fornecedores, calculadora dosagem, lotes |
| 08 | Estoque | Movimentações, pedidos compra, substâncias controladas, scanner |
| 09 | Financeiro | Faturas, recebimentos, cancelamento, NFSe, comissões, conciliação bancária, serviços, mapeamento tipo→serviço |
| 10 | Agendamento | Calendário visual, agendamento online, recorrente, lembretes |
| 11 | Tutores e Pets | Cadastro, microchip/RG, timeline, óbito, portal |
| 12 | Convênios | Cadastro, tabelas, guias, faturamento, claims, CVI |
| 13 | Usuários e Permissões | 12 funções, 284 permissões |
| 14 | Multi-filiais | Estrutura, corporate dashboard, transferências |
| 15 | Relatórios | Clínicos, financeiros, estoque, exportação |
| 16 | Auditoria e LGPD | Trilha auditoria, direitos titular, anonimização |
| 17 | Notificações | Canais, preferências tutor, campanhas |
| 18 | Chat | Mensagens tutor ↔ clínica, anexos |
| 19 | Configurações | Sistema, integrações, identidade visual, auto-update |
| 20 | Emergências | Protocolos de emergência por espécie/gravidade |
| 21 | Mobile | Interface responsiva, modo mobile /m |
| 22 | Triagem | Painel Livewire, classificação Manchester, tempo real |
| 23 | Hospedagem | Boarding, check-in/out, tarefas, banho e tosa |
| 24 | Odontologia | Odontograma, procedimentos, periodontia |
| 25 | Zoonoses | Cadastro, notificação compulsória, relatórios |
| 26 | Pacotes Petshop | Pacotes de banho & tosa/hotel, assinaturas, consumo com economia |

---

## Permissões

O sistema utiliza **Spatie Laravel Permission v7** com **12 papéis**:

| Papel | Slug | Descrição | Permissões |
|-------|------|-----------|------------|
| Super Admin | super-admin | Acesso total irrestrito | ~180+ (todas) |
| Admin | admin | Acesso total ao sistema | ~180+ (todas) |
| Branch Admin | branch-admin | Administração por filial | ~180+ (escopo filial) |
| Veterinarian | veterinarian | Acesso clínico completo | ~120 |
| **Técnico** | **tecnico** | **Execução de tarefas clínicas sem prescrição** | **~7** |
| Receptionist | receptionist | Agenda e cadastro | ~26 |
| Financial | financial | Módulo financeiro | ~18 |
| Super Financial | super-financial | Financeiro global | ~23 |
| Stock Manager | stock-manager | Estoque e farmácia | ~30 |
| Human Resources | human-resources | RH | ~12 |
| Tutor | tutor | Portal do tutor | 4 |
| Auditor | auditor | Apenas leitura | ~80+ |

As permissões seguem o padrão `modulo.acao` (ex: `appointments.create`, `products.view`, `nfse.emit`).

### Categorias de Permissão
- **Admin:** admin.view, users.*, roles.*, branches.*
- **Cadastro:** tutors.*, pets.*, convenios.*
- **Clínico:** medical-records.*, prescriptions.*, vaccinations.*, exams.*, surgeries.*, triage.*
- **Internação:** hospitalizations.*, execution-maps.*
- **Farmácia:** products.*, stock.*, suppliers.*, categories.*, controlled-substances.*
- **Financeiro:** invoices.*, payments.*, nfse.*, nfe.*, commissions.*, bank-reconciliation.*
- **Estoque:** purchase-orders.*, stock.transfer, stock.forecast, stock.reorder
- **Petshop:** pet-shop-packages.*, pet-shop-subscriptions.*
- **Convênios:** convenio-claims.*, insurance.petlove
- **Comunicação:** staff-notes.*, chat.*, communication-templates.*
- **Sistema:** configuracoes.view, docs.view, system-update, branding, nfe-config.edit
- **Outros:** emergency-protocols.*, diet-plans.*, pre-anesthetic.*

---

## Integrações

### Comunicação

#### WhatsApp
Configurado via painel admin em **Configurações > Notificações** (aba WhatsApp). Provedores disponíveis:

| Provedor | Chave de Config | Campos |
|----------|-----------------|--------|
| **Z-API** | `whatsapp_zapi_url/token/instance` | URL da API, Token, Instância |
| **Weni** | `whatsapp_weni_api_key/project_uuid/from_number` | API Key, Project UUID, Número |
| **Cloud API (Meta)** | `whatsapp_cloudapi_access_token/phone_number_id` | Access Token, Phone Number ID |
| **Twilio WhatsApp** | `whatsapp_twilio_account_sid/auth_token/from_number` | Account SID, Auth Token, Número |

**Provider Z-API**: `App\Services\Notification\WhatsApp\ZapiProvider`
**Provider Weni**: `App\Services\Notification\WhatsApp\WeniProvider`
**Provider Cloud API**: `App\Services\Notification\WhatsApp\CloudApiProvider`
**Provider Twilio**: `App\Services\Notification\WhatsApp\TwilioWhatsAppProvider`
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `whatsapp`

#### SMS
| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `communication.sms.url` | `SMS_API_URL` | `https://api.smsprovider.com/v1/send` | URL base da API de SMS |
| `communication.sms.key` | `SMS_API_KEY` | — | Chave de API (Bearer token) |

**Provider**: `App\Services\Communication\SmsProvider`
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `sms`

#### E-mail
Configurado via painel admin em **Configurações > Notificações** (aba E-mail). Os provedores disponíveis são:

| Provedor | Chave de Config | Campos |
|----------|-----------------|--------|
| **MailerSend** | `email_mailersend_api_key` | API Key |
| **SMTP** | `email_smtp_host/port/username/password/encryption` | Servidor, porta, usuário, senha, criptografia |
| **Mailgun** | `email_mailgun_domain/secret/endpoint` | Domínio, Secret, endpoint |
| **SES** | `email_ses_key/secret/region` | Access Key, Secret Key, região |
| **SendGrid** | `email_sendgrid_api_key` | API Key |

**Service**: `App\Services\Notification\NotificationService` (resolve via `notification_config()`)
**Uso**: Comando `ProcessCommunicationQueue` envia mensagens para canal `email`

### NFSe (Service Invoices)

**Adapter**: `App\Services\Nfse\NfseService` — orquestra configuração → payload → emissão → persistência
**Arquitetura**: Adapter Pattern — interface `NfseProvider`, resolvida por `NfseService::resolveProvider()`

**Provedores disponíveis:**

| Provedor | Classe | Autenticação | Endpoints |
|----------|--------|-------------|-----------|
| **Webmania®** | `WebmaniaProvider` | Bearer token (`webmania_access_token`) | `POST /2/nfse/emissao/`, `GET /2/nfse/{id}/`, `PUT /2/nfse/cancelar` |
| **FocusNFe** | `FocusNfeProvider` | Bearer token (`focus_api_token`) | `POST /v2/nfse?ref={ref}`, `GET /v2/nfse/{ref}`, `DELETE /v2/nfse/{ref}` |
| **Spedy** | `SpedyProvider` | API Key (`spedy_api_key`) | `POST /v1/nfse`, `GET /v1/nfse/{id}`, `POST /v1/nfse/{id}/cancelar` |
| **NFE.io** | `NfeIoProvider` | Basic Auth (`api_key` + `company_id`) | `POST /v1/companies/{id}/serviceinvoices`, `GET /v1/companies/{id}/serviceinvoices/{id}`, `DELETE /v1/companies/{id}/serviceinvoices/{id}` |

### NFe / NFC-e (Product Invoices)

**Adapter**: `App\Services\Nfe\NfeService` — orquestra configuração → payload → emissão → persistência
**Arquitetura**: Adapter Pattern — interface `NfeProvider`, resolvida por `NfeService::resolveProvider()`

**Modelos fiscais:**
- **NFC-e** (modelo 65): Nota Fiscal ao Consumidor Eletrônica — emitida automaticamente ao pagar fatura com itens de produto
- **NF-e** (modelo 55): Nota Fiscal Eletrônica — emitida apenas para transferências de estoque entre unidades

**Provedores disponíveis:**

| Provedor | Classe | Autenticação | Endpoints NFC-e | Endpoints NF-e |
|----------|--------|-------------|-----------------|----------------|
| **Webmania®** | `WebmaniaProvider` | Consumer-Key/Secret + Access-Token/Secret | `POST /api/1/nfe/emissao/` (modelo: 65) | `POST /api/1/nfe/emissao/`, `PUT /api/1/nfe/cancelar/` |
| **FocusNFe** | `FocusNfeProvider` | Bearer token (`focus_api_token`) | `POST /v2/nfc?ref={ref}` | `POST /v2/nfe?ref={ref}`, `DELETE /v2/nfe/{ref}` |
| **NFE.io** | `NfeIoProvider` | Basic Auth (`api_key` + `company_id`) | `POST /v2/companies/{id}/consumerinvoices` | `POST /v2/companies/{id}/productinvoices` |

**Persistência:** `App\Models\NfeInvoice` com campo `tipo` (`nfe` ou `nfce`)
**Listeners:** `EmitirNfeOnPaid` — emite NFC-e ao pagar; `StockController::transfer()` — emite NF-e em transferências
**Permissões:** `nfe.view`, `nfe.emit`, `nfe.cancel`, `nfe-config.edit`

### Pagamentos

#### Gateway de Pagamento

**Status:** **PIX** está sempre disponível. **Mercado Pago** ativo para pagamento online (portal do tutor). PagSeguro e Stripe previstos para versões futuras. **Stone** inativo (era exclusivo PDV/maquininha, removido).

**Arquitetura:** Multi-provedor com Interface + Factory Pattern + Service Layer.

| Camada | Arquivo | Descrição |
|--------|---------|-----------|
| Interface | `app/Services/Payment/Contracts/PaymentGatewayProvider.php` | Contrato com `charge()`, `checkout()`, `verifyWebhook()`, `supportedChannels()` |
| Factory | `app/Services/Payment/PaymentGatewayProviderFactory.php` | Mapeia `provider` string → classe concreta |
| Service | `app/Services/Payment/PaymentService.php` | Orquestra `charge`/`checkout`/`processWebhook` com fallstack |
| Controller | `app/Http/Controllers/Api/PaymentWebhookController.php` | Endpoint `/api/payments/webhook/{gateway}` (sempre 200) |

**Providers ativos:**

| Provider | Classe | SDK | Canais |
|----------|--------|-----|--------|
| PIX | `PixStaticProvider` + `PixService` | `endroid/qr-code` | portal |
| Mercado Pago | `MercadoPagoProvider` | `mercadopago/dx-php` | portal (checkout cartão, saldo) |

**Providers inativos (código existente):**

| Provider | Classe | SDK | Motivo |
|----------|--------|-----|--------|
| PagSeguro | `PagSeguroProvider` | `pagseguro/pagseguro-php-sdk` | Previsto |
| Stripe | `StripeProvider` | `stripe/stripe-php` | Previsto |
| Stone | `StoneProvider` | HTTP direto (OAuth) | Removido (era PDV/maquininha) |

**Serviço PIX:**

| Chave | Variável .env | Padrão | Descrição |
|-------|---------------|--------|-----------|
| `pix.pix_key` | `PIX_KEY` | `admin@vetessence.com` | Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória) — substituída pelo `public_key` do gateway quando configurado |
| `pix.gi` | `PIX_GI` | `br.gov.bcb.pix` | GUI (identificador do arranjo de pagamentos) |
| `pix.merchant_name` | `PIX_MERCHANT_NAME` | `VETESSENCE CLINICA VETERINARIA` | Nome do recebedor (até 25 caracteres) |
| `pix.city` | `PIX_CITY` | `SAO PAULO` | Cidade do recebedor |
| `pix.url` | `PIX_URL` | — | URL opcional para payload dinâmico |

**Geração do Payload PIX:**
- `PixService::buildMerchantAccountInformation()` — constrói Merchant Account Information (tag 26) com subcampos GUI (`00`) e chave PIX (`01`)
- `PixService::buildPayload()` — monta o payload EMV completo com TLV: `000201` + MAI + MCC + moeda + país + nome + cidade + valor + txid + CRC
- `PixService::generateQRCode()` — gera o QR Code PNG via `endroid/qr-code`

**Testes:** `tests/Unit/Services/PixServiceTest.php` (9 testes)

**Tabela `payment_gateways` (colunas principais):**

| Campo | Descrição |
|-------|-----------|
| `provider` | Nome do provedor (`pix` ou `mercadopago`) |
| `channel` | Canal: `portal` (PDV removido; `both` mantido como retrocompatível — tratado como `portal`) |
| `public_key` | Chave PIX (CPF, CNPJ, e-mail, telefone ou EVP) |
| `branch_id` | Unidade específica ou null (todas as unidades) |
| `config` | JSON com configurações adicionais (ex: `url` para PIX dinâmico) |

![Fluxo de Pagamento PIX](../diagrams/32-fluxo-pagamento-gateway.svg)

### APIs Externas

#### Estoque Inteligente (StockForecastService)
**Service**: `App\Services\StockForecastService`
**Métodos**:
- `getAverageDailyConsumption(Product $product, ?int $days = 90)`: calcula consumo médio diário
- `needsReorder(Product $product)`: verifica se estoque está abaixo do ponto de reposição
- `suggestPurchaseOrder(?Branch $branch = null)`: retorna sugestões de compra
- `expiringProducts(?Branch $branch = null, ?int $days = 30)`: lista produtos próximos ao vencimento
- `recalculateConsumption(?Branch $branch = null)`: recalcula consumo para todos os produtos

**Comandos Artisan**:
- `stock:forecast --recalculate`: recalcula consumo médio (agendado 03:00)
- `stock:forecast --alert-expiry`: alerta sobre vencimentos (agendado 06:00)
- `subscriptions:renew`: renova assinaturas petshop expiradas

**Agendamento (Kernel)**:
```php
$schedule->command('stock:forecast --recalculate')->dailyAt('03:00');
$schedule->command('stock:forecast --alert-expiry')->dailyAt('06:00');
```

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

#### Petlove (Insurance Provider)
**Provider**: `App\Services\Insurance\PetloveProvider` (implementa `InsuranceProvider` interface)
**Chave**: `'petlove'` registrada em `InsuranceProviderFactory`
**Métodos**: `checkEligibility()`, `requestPreAuthorization()`, `submitClaim()`, `checkStatus()`
**Testes**: `tests/Unit/Services/Insurance/PetloveProviderTest.php` (11 testes)

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
| `POST /api/payments/webhook/{gateway}` | Sim | Sem limite | Webhook de pagamento (sempre retorna 200) |
| `POST /api/webhooks/nfse/{branch_id}` | Sim | 60/min | Callback de atualização NFSe |
| `POST /api/v1/lab-equipment/{id}/receive` | Sim | — | Recebimento de resultados lab |
| `GET /api/v1/lab-equipment/{id}/status` | Sim | — | Status de equipamento lab |

---

## Testes

### Suite Completa

| Suite | Count | Descrição |
|-------|-------|-----------|
| Unit/Models | ~378 | Todos os modelos |
| Feature/Controllers | ~780 | Todos os controllers |
| Feature/Commands | ~25 | Comandos Artisan |
| Feature/Integrations | ~12 | Fluxos completos |
| Feature/Api | ~18 | Endpoints de API |
| Feature/Portal | ~20 | Portal do tutor |
| Feature/Services | ~213 | Serviços (Payment, NFSe, comunicação, etc.) |
| Livewire | ~222 | Componentes Livewire |
| Unit/Services | ~30 | Pix, EmailApi, BranchContext, StockForecast, Petlove |
| **Total** | **~2.045** | **397 files, 1.812 methods, 6.420 assertions** |

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
| `PIX_KEY` | Chave PIX (também configurável via painel) |
| `PIX_MERCHANT_NAME` | Nome do recebedor PIX |
| `PIX_CITY` | Cidade do recebedor PIX |
| `MERCADO_PAGO_ACCESS_TOKEN` | Access Token Mercado Pago |
| `MERCADO_PAGO_PUBLIC_KEY` | Public Key Mercado Pago |
| `NFEIO_API_KEY` | API Key NFE.io (NFe e NFSe) |
| `SESSION_DRIVER` | Driver de sessão (file, database, redis) |
| `QUEUE_CONNECTION` | Driver de fila (sync, database, redis) |

---

## Deploy

### 1. Preparação do Servidor (comum a demo e produção)

```bash
# Atualizar SO
apt update && apt upgrade -y

# Instalar dependências do sistema
apt install -y nginx mysql-server-8.0 redis-server supervisor \
  git curl wget unzip gnupg2 fail2ban ufw

# PHP 8.4 (Ondrej PPA)
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.4-fpm php8.4-cli php8.4-common \
  php8.4-bcmath php8.4-gd php8.4-intl php8.4-mbstring \
  php8.4-mysql php8.4-xml php8.4-zip php8.4-curl php8.4-redis

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Node.js 20+
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Firewall
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
```

---

### 2. Instalação de Demonstração (servidor único)

Aplicável quando app e banco rodam na mesma máquina (mínimo 2 GB RAM, 1 core).

```bash
# 2.1. Configurar MySQL
mysql -u root -p <<SQL
CREATE DATABASE vetessence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vetessence'@'localhost' IDENTIFIED BY 'SUA_SENHA_AQUI';
GRANT ALL PRIVILEGES ON vetessence.* TO 'vetessence'@'localhost';
FLUSH PRIVILEGES;
SQL

# 2.2. Clonar e instalar
cd /var/www
git clone https://github.com/hectordufau/vetessence.git
cd vetessence
cp .env.example .env

# 2.3. Ajustar .env (editar manualmente ou via sed)
sed -i "s/DB_DATABASE=.*/DB_DATABASE=vetessence/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=vetessence/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=SUA_SENHA_AQUI/" .env

# 2.4. Instalar dependências e buildar
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2.5. Configurar Laravel
php artisan key:generate
php artisan livewire:publish
php artisan vendor:publish --tag=livewire:assets --force
php artisan migrate --seed
php artisan storage:link
php artisan docs:publish

# 2.6. Permissões
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2.7. Configurar Nginx (ver exemplo abaixo)
# 2.8. Configurar PHP-FPM para ambiente demo
sed -i 's/pm.max_children =.*/pm.max_children = 6/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.start_servers =.*/pm.start_servers = 2/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.min_spare_servers =.*/pm.min_spare_servers = 1/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.max_spare_servers =.*/pm.max_spare_servers = 3/' /etc/php/8.4/fpm/pool.d/www.conf
systemctl restart php8.4-fpm

# 2.9. HTTPS (se houver domínio)
certbot --nginx -d seu-dominio.com
```

---

### 3. Instalação de Produção (2 servidores)

#### 3.1. Servidor de Banco de Dados

```bash
# Instalar MySQL
apt install -y mysql-server-8.0

# Editar /etc/mysql/mysql.conf.d/mysqld.cnf
cat >> /etc/mysql/mysql.conf.d/mysqld.cnf <<'EOF'
[mysqld]
bind-address = 0.0.0.0
max_connections = 500
innodb_buffer_pool_size = 12G        # 70-80% da RAM (16 GB)
innodb_log_file_size = 1G
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
query_cache_type = 0
tmp_table_size = 256M
max_heap_table_size = 256M
EOF

systemctl restart mysql

# Criar banco e usuário com acesso do servidor app
mysql -u root <<SQL
CREATE DATABASE vetessence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vetessence'@'IP_DO_SERVIDOR_APP' IDENTIFIED BY 'SENHA_FORTE';
GRANT ALL PRIVILEGES ON vetessence.* TO 'vetessence'@'IP_DO_SERVIDOR_APP';
FLUSH PRIVILEGES;
SQL

# Firewall: liberar apenas app server
ufw allow from IP_DO_SERVIDOR_APP to any port 3306
```

#### 3.2. Servidor de Aplicação

```bash
# 3.2.1. Clonar e instalar (mesmo que demo)
cd /var/www
git clone https://github.com/hectordufau/vetessence.git
cd vetessence
cp .env.example .env

# 3.2.2. .env para produção
sed -i "s/DB_HOST=.*/DB_HOST=IP_DO_SERVIDOR_BANCO/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=vetessence/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=vetessence/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=SENHA_FORTE/" .env
sed -i "s/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/" .env
sed -i "s/CACHE_DRIVER=.*/CACHE_DRIVER=redis/" .env
sed -i "s/SESSION_DRIVER=.*/SESSION_DRIVER=redis/" .env
sed -i "s/REDIS_HOST=.*/REDIS_HOST=127.0.0.1/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env

# 3.2.3. Dependências
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan livewire:publish
php artisan vendor:publish --tag=livewire:assets --force
php artisan migrate --seed
php artisan storage:link
php artisan docs:publish

# 3.2.4. Permissões
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3.2.5. Otimização Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### 3.3. Nginx — Virtual Host

**Passo 1 — Configuração inicial (porta 80 apenas):**

```nginx
# /etc/nginx/sites-available/vetessence
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/vetessence/public;

    index index.php;

    charset utf-8;
    client_max_body_size 50M;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript image/svg+xml;
    gzip_min_length 256;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.env$ {
        deny all;
    }

    # Cache de assets estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2?)$ {
        expires 365d;
        add_header Cache-Control "public, immutable";
    }

    # Logs
    access_log /var/log/nginx/vetessence_access.log;
    error_log  /var/log/nginx/vetessence_error.log;
}
```

Ativar e reiniciar:
```bash
ln -s /etc/nginx/sites-available/vetessence /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx
```

**Passo 2 — Após executar `certbot --nginx`:**

O Certbot altera automaticamente o virtual host para incluir SSL e redirecionamento HTTP → HTTPS. O resultado final será equivalente a:

```nginx
# /etc/nginx/sites-available/vetessence

# Redirecionamento HTTP → HTTPS
server {
    listen 80;
    server_name seu-dominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seu-dominio.com;
    root /var/www/vetessence/public;

    index index.php;

    charset utf-8;
    client_max_body_size 50M;

    # SSL (gerado pelo Certbot)
    ssl_certificate     /etc/letsencrypt/live/seu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seu-dominio.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Segurança
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript image/svg+xml;
    gzip_min_length 256;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.env$ {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2?)$ {
        expires 365d;
        add_header Cache-Control "public, immutable";
    }

    access_log /var/log/nginx/vetessence_access.log;
    error_log  /var/log/nginx/vetessence_error.log;
}
```

> **Nota:** O Certbot cria os arquivos `options-ssl-nginx.conf` e `ssl-dhparams.pem` em `/etc/letsencrypt/`. A renovação automática via snap mantém esses arquivos atualizados. Não edite manualmente os caminhos dos certificados — o Certbot os gerencia.

#### 3.4. PHP-FPM (Produção)

```bash
# Editar /etc/php/8.4/fpm/pool.d/www.conf
# Para servidor com 8 GB RAM e 4 cores:
#   pm = dynamic
#   pm.max_children = 16
#   pm.start_servers = 4
#   pm.min_spare_servers = 2
#   pm.max_spare_servers = 8
#   pm.max_requests = 500

sed -i 's/pm.max_children =.*/pm.max_children = 16/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.start_servers =.*/pm.start_servers = 4/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.min_spare_servers =.*/pm.min_spare_servers = 2/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/pm.max_spare_servers =.*/pm.max_spare_servers = 8/' /etc/php/8.4/fpm/pool.d/www.conf

systemctl restart php8.4-fpm
```

#### 3.5. Redis

```bash
# /etc/redis/redis.conf
sed -i 's/# maxmemory <bytes>/maxmemory 1gb/' /etc/redis/redis.conf
sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
systemctl restart redis-server
```

#### 3.6. Supervisor — Workers de Fila

```ini
# /etc/supervisor/conf.d/vetessence-worker.conf
[program:vetessence-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vetessence/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/vetessence/queue-worker.log
stopwaitsecs=3600
```

```bash
mkdir -p /var/log/vetessence
supervisorctl reread
supervisorctl update
supervisorctl start all
```

#### 3.7. Cron — Agendador Laravel (Obrigatório)

O Laravel Scheduler executa comandos agendados (estoque inteligente, claims, alertas, etc.). Sem ele, nenhum comando automático funciona.

Adicione esta linha ao crontab do servidor (uma única vez):

```bash
echo "* * * * * www-data cd /var/www/vetessence && php artisan schedule:run >> /dev/null 2>&1" \
  | sudo tee /etc/cron.d/vetessence-scheduler
sudo chmod 644 /etc/cron.d/vetessence-scheduler
```

Substitua `/var/www/vetessence` pelo caminho real da instalação e `www-data` pelo usuário do PHP-FPM.

**Verificação:**
```bash
# Confirmar que o arquivo existe
cat /etc/cron.d/vetessence-scheduler

# Testar o scheduler manualmente (não espera o minuto)
php artisan schedule:list          # lista comandos agendados
php artisan schedule:run           # executa comandos devidos
```

**Comandos agendados atuais:**

| Comando | Horário | Descrição |
|---------|---------|-----------|
| `stock:forecast --recalculate` | 03:00 | Recalcula consumo médio de estoque |
| `stock:forecast --alert-expiry` | 06:00 | Alerta sobre lotes próximos ao vencimento |

> **Importante:** o `schedule:run` deve rodar **a cada minuto** via cron. O próprio Laravel decide se cada comando agendado deve ou não executar naquele minuto, baseado no horário configurado no Kernel.

#### 3.8. SSL com Let's Encrypt

No **Ubuntu 22.04+**, instale o Certbot via **snap** (recomendado pela Let's Encrypt):

```bash
# Remover versão antiga via apt (se existir)
apt remove -y certbot python3-certbot-nginx 2>/dev/null

# Instalar via snap
snap install certbot --classic

# Garantir que o comando certbot está no PATH
ln -sf /snap/bin/certbot /usr/bin/certbot
```

> **Alternativa via apt:** Caso prefira não usar snap, ative o repositório `universe` e instale `apt install -y certbot python3-certbot-nginx`. A versão via apt pode ser mais antiga, mas é funcional.

Emitir certificado e configurar Nginx automaticamente:

```bash
certbot --nginx -d seu-dominio.com --non-interactive --agree-tos -m admin@seu-dominio.com
```

Verificar renovação automática (o snap já configura o timer):

```bash
certbot renew --dry-run
```

#### 3.8. Segurança

```bash
# fail2ban para Nginx
cat > /etc/fail2ban/jail.local <<'EOF'
[nginx-http-auth]
enabled  = true
port     = http,https
filter   = nginx-http-auth
logpath  = /var/log/nginx/*error.log
maxretry = 5
findtime = 600
bantime  = 3600
EOF
systemctl restart fail2ban

# Desabilitar root SSH
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
systemctl restart sshd

# Permissões restritas
chmod 640 /var/www/vetessence/.env
```

#### 3.9. Backup

```bash
# Script de backup diário (/usr/local/bin/backup-vetessence.sh)
#!/bin/bash
BACKUP_DIR="/backups/vetessence"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

# Banco
mysqldump --single-transaction --routines --events \
  -u vetessence -p'SENHA' vetessence | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Storage (arquivos enviados)
rsync -a /var/www/vetessence/storage/app "$BACKUP_DIR/storage_$DATE/"

# Reter 30 dias
find "$BACKUP_DIR" -name "db_*.sql.gz" -mtime +30 -delete
find "$BACKUP_DIR" -name "storage_*" -mtime +30 -exec rm -rf {} \;
```

```bash
chmod +x /usr/local/bin/backup-vetessence.sh
echo "0 3 * * * root /usr/local/bin/backup-vetessence.sh" > /etc/cron.d/vetessence-backup
```

#### 3.10. Monitoramento (opcional)

```bash
# Verificar workers
supervisorctl status

# Logs da aplicação
tail -f /var/www/vetessence/storage/logs/laravel.log

# Health check
curl https://seu-dominio.com/up
```

> **Nota:** O endpoint `/up` retorna HTTP 200 se o servidor estiver operacional, sem exigir autenticação. Útil para health checks de balanceadores.

---

### 4. Manutenção

Após atualizar o Livewire via `composer update`, republicar os assets:

```bash
php artisan livewire:publish
php artisan vendor:publish --tag=livewire:assets --force
php artisan optimize:clear
```

Manutenção geral:

```bash
php artisan down              # Modo manutenção
php artisan up                # Reativar
php artisan optimize:clear    # Limpar cache após alterações
php artisan docs:publish      # Republicar documentação
php artisan livewire:publish  # Republicar config/assets Livewire
php artisan vendor:publish --tag=livewire:assets --force
```

### 5. Auto-Update

#### 5.1. Atualização via painel

Admin pode atualizar via painel em **Configurações > Atualizar Sistema**.
Requer token GitHub configurado e `exec()` habilitado no servidor.

Fluxo executado pelo sistema:
```bash
php artisan down
git pull https://token@github.com/hectordufau/vetessence.git
php artisan migrate --force
php artisan livewire:publish
php artisan vendor:publish --tag=livewire:assets --force
php artisan optimize:clear
php artisan up
```

#### 5.2. Forçar sincronização (quando há conflitos locais)

Se o servidor tiver modificações locais (ex.: assets do Livewire republicados) que impeçam o `git pull`, force a sobrescrita com o remoto:

```bash
git fetch origin
git reset --hard origin/main
```

> **Atenção:** `git reset --hard` descarta **todas** as alterações locais não comitadas. Execute apenas quando tiver certeza de que não há dados locais a preservar. Após o reset, siga os passos de manutenção (migrate, publish, cache).

### 6. .env — Variáveis Essenciais

As variáveis abaixo **devem** ser configuradas antes de iniciar:

| Variável | Obrigatório | Descrição |
|----------|-------------|-----------|
| `APP_URL` | Sim | URL completa do sistema (ex: `https://vetessence.com.br`) |
| `DB_HOST` | Sim | Host do MySQL (local ou remoto) |
| `DB_DATABASE` | Sim | Nome do banco |
| `DB_USERNAME` | Sim | Usuário do banco |
| `DB_PASSWORD` | Sim | Senha do banco |
| `QUEUE_CONNECTION` | Sim* | `sync` (demo) ou `redis` (produção) |
| `SESSION_DRIVER` | Sim* | `file` (demo) ou `redis` (produção) |
| `REDIS_HOST` | Produção | Host do Redis (usualmente `127.0.0.1`) |

Para variáveis de integração (WhatsApp, NFSe, PIX, etc.), veja a seção [Integrações](#integrações).

---

## Documentação do Sistema

- **Source**: `resources/docs/` (arquivos .md)
- **Published**: `storage/docs/` (via comando `docs:publish`)
- **Admin route**: `/docs` (requer permissão `docs.view`)
- **Tutor route**: `/portal/docs` (autenticado como tutor)
- **Controllers**: `DocController` + `Portal\DocController`
- **Conteúdo**:
  - Manual do Usuário: 26 módulos (01 a 26)
  - Manual Técnico: Este documento
  - Changelog
  - Manual do Tutor: 13 tópicos

---

## Diagrama do Processo

![Matriz de Perfis RACI](../diagrams/matriz-perfis.svg)
*Clique na imagem para ampliar. Diagrama de Atividades UML com raias — retângulos = atividades, losangos = decisão, setas = fluxo entre atividades, raias = atores.*

---

## Diagrama do Processo

![Recursos Humanos](../diagrams/rh-fluxo-admissao.svg)
*Clique na imagem para ampliar. Diagrama de Atividades UML com raias — retângulos = atividades, losangos = decisão, setas = fluxo entre atividades, raias = atores.*

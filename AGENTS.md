# Diretrizes

- Commits: sempre git add -A, commitar e dar push em todas as alterações ao final de cada tarefa.
- O script install/install.sh pergunta email e senha para o primeiro usuário super-admin (padrão super@vet.com).

## Summary

### Done
- **Permission audit + middleware**: added `can:` middleware to 40 controllers; mapped all 46 sidebar menu items to permission gates
- **AuthorizationException handler**: redirects to `route('dashboard')` with flash error instead of 403
- **WYSIWYG rendering**: fixed `{{ }}` → `{!! !!}` in 6 show views
- **`is_veterinarian` field**: migration, model fillable/casts, UserForm checkbox, UserFactory, 21+ query updates to include `orWhere('is_veterinarian', true)` in vet listings
- **Pet edit form**: tutor field read-only in edit mode
- **Fixed pre-existing failures**: PetController/PetDeathRecord views created, LabOrder/HospitalizationCycle/PatientTimeline fixes
- **WYSIWYG validation feedback**: JS sync of is-invalid → TinyMCE red border; @error spans on all 78 wysiwyg fields
- **Removed `required` from wysiwyg textareas** (fixes browser HTML5 validation error on hidden fields)
- **Timezone**: config/app.php changed from UTC → America/Sao_Paulo so `date('H:i')`, `Carbon::now()` reflect Brazil time
- **Auto-update melhorias**: comando Artisan `php artisan system:update` com backup + git pull + migrate; backup automático (mysqldump) antes de aplicar no controller; rate limit de 30 min entre atualizações; view exibe contagem regressiva e desabilita botão
- **Comunicação liberada para todos os perfis**: migration `grant_communication_permissions_to_all_roles` concede `staff-notes.view/create` e `chat.view/create` para Financeiro, Super Financeiro, Estoque, Recursos Humanos e Tutor; código com middleware `can:` e `@can`/`@role` nos sidebars mantido intacto.

### Fixes aplicados (nesta sessão)
- **NfeConfigFactory**: criada (faltava)
- **NfseServiceTest**: `resolveProvider` tests usam factory instance diretamente em vez de `NfseConfig::first()` (evita data pollution)
- **GenerateInvoiceFromAppointment**: adicionados `use App\Models\Tutor` e `use App\Models\Pet` (type hints quebrados)
- **NfeConfigControllerTest**: campo `webmania_app_id/secret` removido (não existem mais); adicionados `webmania_access_token/secret`
- **StockController**: `notes` field com null coalescing em transferências (evita "Undefined array key")
- **AutoInvoiceTest**: usuário recebe permissão `appointments.view` para passar middleware `can:atendimentos`
- **Data pollution**: testes que sofriam com dados residuais agora desativam configs pré-existentes ou escopam queries por IDs criados (StockTransferNfeTest, NfseConfigTest, NfseServiceTest)
- **Round 2 — todas as 24 falhas remanescentes corrigidas**: UniqueConstraint (State/Branch/PurchaseOrder/TreatmentPlan/ZoonoticDisease — `faker->unique()` em factories); scopes (BankTransaction/CommissionLog/PurchaseOrder/TreatmentPlan/VaccineProtocol/ZoonoticDisease — `whereIn('id', $ids)`); SyncSpatieRolesTest (output em português); LlmServiceTest (prompt atualizado); StockDeductionServiceTest/StockForecastServiceTest (assertions resilientes); EmptyStateTest + BranchTest Feature + PaymentGateway Feature (auth + campos obrigatórios)
- **Tecnospeed removido**: provider, config, `NfseService::resolveProvider`, `NfseConfig::$fillable`, migration, teste, docs

### MailerSend + WhatsApp + Mercado Pago (esta sessão)

**Feito:**
- **MailerSend provider**: `app/Services/Notification/Email/MailerSendProvider.php` implementando `EmailProvider` com SDK send + attachments
- **Z-API fix**: URL corrigida para `https://api.z-api.io/instances/{instance}/token/{token}/send-text`; telefone sanitizado (digits only, DDI 55); `Authorization: Bearer` removido
- **NotificationLog**: `VaccinationReminderController::send()` agora cria `NotificationLog` (tipo `vaccine_reminder`) — consultável em Conf. Sistema → Logs de Notificação
- **Mercado Pago restrito a Portal**: PDV/maquininha removido; `charge()` retorna erro; `supportedChannels()` → `['portal']`; `RuntimeEnviroment` → `MercadoPagoConfig::SERVER`/`LOCAL`
- **PDV removido**: `channel` validation restrito a `portal`; opções `pdv`/`both` removidas dos formulários; JS morto (`startPdvCharge`/`copyPdvPix`) removido de `invoices/show.blade.php`; `InvoiceController::show()` não passa mais `$hasPdvGateway`
- **Retrocompatibilidade `both`**: `scopeByChannel('portal')` busca `portal` e `both`; `isPortal()` retorna true para `both`; `getActiveGatewayForChannel('portal')` no PaymentService também busca ambos; `Portal/InvoiceController` idem
- **Stone inativo**: provider fica sem uso (era exclusivo PDV/maquininha)
- **Testes corrigidos**: `PaymentServiceTest` faz `withoutBranch()->update(['is_active' => false])` antes de criar gateway (data pollution); 34 testes passando (PaymentGateway unit/feature/controller + Invoice/Portal)

### Suite Status
- **674+ passed, 1 skipped** (SoftDeleteTest — intencional), **0 failures** ✅
- Data import commands (DbImport*) and DemoSeed intentionally not tested (one-shot scripts)

### PROJETO TREINAMENTO

Para retomar a criação dos vídeos de treinamento, chame por **PROJETO TREINAMENTO**.

**Regras:**
- Erros encontrados durante execução do roteiro (Laravel exceptions, 405/500, validação no form) devem interromper a gravação para correção no sistema (código ou engine).
- O engine `treinamento.py` detecta automaticamente páginas de erro Laravel/Symfony e aborta o roteiro com `RuntimeError`.
- Apenas roteiros 100% bem-sucedidos (sem erros e sem warnings de opção não encontrada) geram vídeo final.
- Se um roteiro é interrompido, os dados criados até aquele momento devem ser removidos antes de reexecutar, para evitar duplicidade e falsos positivos.
- Cada roteiro deve começar com um passo `shell` que executa `php artisan treinamento:cleanup --module=<modulo>` para remover dados residuais.
- Use os screenshots em `/tmp/treinamento_screenshots/` para diagnosticar bugs.
- Bugs corrigidos ficam registrados nesta seção.

**Feito:**
- Fase 01 (`11-tutores-pets`): roteiro + vídeo validado em `~/Videos/VetEssence/11-tutores-pets_20260710_095540.mp4`
- Engine: `bin/treinamento.py` (Selenium + ffmpeg, helpers: `selecionar_tom_select`, `clicar_submit_modal` com scroll suave, `scroll_smoothly_modal`)
- 5 roteiros definidos em `bin/roteiros.py`

**Nesta sessão:**
- Roteiro `07-farmacia` implementado: 49 passos, login `super@vet.com`, cria categoria "Medicamentos" → fornecedor "FarMed Distribuidora" → produto "Dipirona 500mg" (c/ TomSelects de categ/fornec) → ajuste estoque 100 un → fármaco "Dipirona Sódica" → lista produtos → logout
- Engine: `selecionar_tom_select` com fallback para `select[name=...]` (necessário p/ form de estoque)
- **Bugs corrigidos durante execução do roteiro**:
  - `#` (Python comment) dentro de f-string JS → JS syntax error (trocado por `//`)
  - `el.clear()` em `<select>` → InvalidElementState (agora usa JS value + events)
  - `wire:model="species"` duplicado (drug-formulary + dosage-calculator) → `preencher_livewire` busca modal aberto primeiro
  - `logout()` usava `GET /logout` → 405 Method Not Allowed (agora clica link "Sair" que faz POST via form oculto)
  - Engine não detectava erros Laravel → adicionado `verificar_erro_laravel()` após cada ação
- **Cleanup**: comando `php artisan treinamento:cleanup --module=07-farmacia` adiciona passo `shell` obrigatório no início do roteiro para remover dados residuais
- Vídeo `07-farmacia_20260721_084119.mp4` gravado (obs: contém erro 405 do logout, será regravado)
**Próximo passo:**
```bash
python3 bin/treinamento.py --modulo 10-agendamento
```

```

### PROJETO WEBMANIA

Para retomar a integração Webmania (NFe/NFCe e NFSe), chame por **PROJETO WEBMANIA**.

**Feito nesta sessão:**
- **NFe/NFCe WebmaniaProvider**: auth corrigida (4 headers: Consumer-Key, Consumer-Secret, Access-Token, Access-Token-Secret); base URL `webmania.com.br/api/1`; endpoint `/nfe/emissao/`; ambiente int 1/2
- **NFSe WebmaniaProvider**: auth Bearer token v2.0; base URL `api.webmania.com.br`; endpoint `/2/nfse/emissao/`; payload com `rps[]`
- **Migrations**: add `webmania_access_token` e `webmania_access_token_secret` em `nfe_configs`; add `webmania_access_token` em `nfse_configs`
- **Telas de config**: campos `webmania_app_id/secret` removidos; `webmania_access_token` e `webmania_access_token_secret` adicionados (NFe); `webmania_access_token` único para NFSe
- **NF-e de transferência**: migration `nfe_transfers`, model `NfeTransfer`, método `emitirTransferencia()` no `NfeService` + `WebmaniaProvider`, checkbox no form de transferência, implementação no `StockController::transfer()`
- **Página de Configuração Unificada NF**: `NfConfigController`, view `resources/views/nf/config.blade.php` com ambas configs NFe e NFSe em cards lado a lado; rota `/nf/config`; link "Config. NF" no sidebar (menu Conf. Sistema)
- **Sidebar NF**: links para "NFS-e" e "NF-e" adicionados no menu Faturamento; "Config. NF" no menu Conf. Sistema
- **Cancelamento Webmania corrigido**: NF-e usa `PUT /api/1/nfe/cancelar/` com `chave` no body (era POST com nfe_number na URL); NFS-e usa `PUT /api/2/nfse/cancelar` com `uuid` no body (era POST). Interfaces atualizadas com parâmetros opcionais `nfeKey`/`uuid`
- **Tests**: NFe/NFSe providers corrigidos, `NfeTransferTest`, `StockTransferNfeTest` — aguardando chaves Webmania para execução

**Próximo passo (quando chegar as chaves):**
```bash
php artisan tinker
# Configurar credenciais na tela de config e testar:
>>> $config = App\Models\NfeConfig::first();
>>> $nfseConfig = App\Models\NfseConfig::first();
# Testar emissão NFe/NFCe e NFSe manualmente
```
Requer chaves de homologação Webmania para validar fluxo completo.

### PROJETO NFE.IO

Para retomar a integração NFE.io (NFe e NFS-e), chame por **PROJETO NFE.IO**.

**Feito nesta sessão:**
- **NFE.io Provider NFe (v2 Product Invoices)**: reescrito para API `https://api.nfse.io/v2/companies/{companyId}/productinvoices` com auth `Authorization: Basic {base64(apiKey)}`; endpoints emitir/consultar/cancelar
- **NFE.io Provider NFS-e (v1 Service Invoices)**: reescrito para API `https://api.nfe.io/v1/companies/{companyId}/serviceinvoices` com auth `Authorization: Basic {base64(apiKey)}`; endpoints emitir/consultar/cancelar
- **Migration**: `add_nfeio_company_id_to_nf_configs` adiciona `nfeio_company_id` em `nfe_configs` e `nfse_configs`
- **Models**: fillable `nfeio_company_id` em `NfeConfig` e `NfseConfig`
- **Controllers**: validação `nfeio_company_id` em `NfeConfigController` e `NfseConfigController`
- **View**: campo `company_id` adicionado no form de config NF (ambos NFe e NFS-e)
- **Config**: `config/nfe.php` com `base_url` apontando para `https://api.nfse.io`
- **Tests**: 12/12 passando (Unit NFe + Feature NFS-e)
- **Banco**: `.env` e `.env.testing` com `DB_DATABASE=vetessence` (MySQL 192.168.0.150)

**Próximo passo:**
```bash
php artisan tinker
# Configurar credenciais na tela /nf/config e testar:
>>> $config = App\Models\NfeConfig::first();
>>> $nfseConfig = App\Models\NfseConfig::first();
```
Requer chave de API NFE.io + company_id para validar fluxo completo.

### PROJETO NFE.IO (NFS-e OK, NFC-e OK, NF-e pendente)

**NFS-e (Service Invoices) — FUNCIONANDO ✅**
- Base URL: `https://api.nfe.io/v1/companies/{companyId}/serviceinvoices`
- Auth: `Authorization: Basic <raw_api_key>` (sem base64 — SDK oficial confirma)
- Company ID real: `7bf5d244cee3452389949fa50d1ceb7e` (environment: Development)
- Emissão testada e aprovada: Status 202, `flowStatus: "WaitingCalculateTaxes"`
- Persistência: `NfseInvoice` criado com `provider_response` completo, `nfse_status='issued'`
- CPF válido obrigatório (check digits validados pela API): usado `52998224725`
- `city.code` no borrower: fallback para `$branch->municipio_ibge` (já que `tutors` não tem `city_ibge`)
- `phoneNumber` no borrower: sempre enviado (tutor → branch → padding 8 zeros)
- `$response->json()` pode retornar string em vez de array → guard `is_array()` em ambos providers (NFe/NFSe)
- Tests: 15/15 passando (NfeIoProviderTest)

**NFC-e (Consumer Invoices) — IMPLEMENTADO ⚡**
- Após pagamento da fatura, itens produto geram NFC-e (não mais NF-e)
- Provider `emitirNfce()` adicionado à interface `NfeProvider` + implementações:
  - **Webmania**: mesmo endpoint `/nfe/emissao/` com `modelo: '65'`
  - **NFE.io**: endpoint `/v2/companies/{id}/consumerinvoices`
  - **FocusNFe**: endpoint `/v2/nfc?ref={ref}`
- `NfeService::emitirNfce()` com persistência em `NfeInvoice` (tipo='nfce')
- `NfeInvoice.tipo` adicionado (migration: `add_tipo_to_nfe_invoices`)
- Lista NFC-e em `/nfce` com controller + views próprios
- Sidebar: menu "NFC-e" abaixo de Financeiro (entre NFS-e e NF-e)
- Invoice show: card "NFC-e" (era "NF-e"), botão "Emitir Nota Fiscal" corrigido
- NF-e fica apenas para transferência entre unidades (NfeTransfer)

**NF-e (Product Invoices) — Parcial ⚠️**
- Base URL: `https://api.nfse.io/v2/companies/{companyId}/productinvoices`
- Payload v2: `environment`, `buyer`, `items[].tax.icms`
- ICMS para Simples Nacional: `csosn` (não `cst`)
- **Bloqueado**: NFE.io exige `stateTax` (Inscrição Estadual) + certificado digital
- `/nfe` lista apenas registros `tipo='nfe'` (NfeTransfer futuramente)

**Próximo passo NF-e:**
1. Fazer upload do certificado digital no painel NFSe.io (empresa `7bf5d244cee3452389949fa50d1ceb7e`)
2. Configurar Inscrição Estadual (state tax) no painel
3. Testar emissão via tinker

### Documentação — Auditoria e Correções (esta sessão)

**Auditoria**: 127 discrepâncias encontradas entre docs e código real.

**Lote 1 (sessão anterior):**
- `index.md` (user-manual): 26 módulos + seção tutor (PIX, não cartão/boleto)
- `tutor-manual/index.md`, `09-faturas.md`, `12-duvidas-frequentes.md`, `04-agendamento.md`
- `user-manual/09-financeiro.md`: NFC-e, NF-e transferência, 4 provedores NFSe, FocusNFe, diagrama de roteamento

**Lote 2 (corrigido nesta sessão):**
- `user-manual/index.md`: descrição módulo 09 (NFC-e + NF-e transferência), Acesso Rápido (NFC-e, Config NF), seção Matriz Perfis RACI (corrigido título duplicado)
- `user-manual/08-estoque.md`: NFC-e no lugar de NF-e em venda; NF-e checkbox na transferência
- `user-manual/10-agendamento.md`: status traduzidos (Em Andamento, Finalizado, Cancelado, Não Compareceu)
- `user-manual/13-usuarios-e-permissoes.md`: perfil Técnico adicionado; Tutor corrigido (4 permissões, não 0)
- `user-manual/17-notificacoes.md`: MailerSend adicionado; NotificationLog descrito; Vaccine Reminder logs
- `user-manual/19-configuracoes.md`: MailerSend; Mercado Pago ativo (não "previsto"); PDV/channel removido; Config NF unificada (NFS-e + NFC-e/NF-e cards); backup automático; FocusNFe; Webmania NFSe Bearer auth
- `technical-manual/index.md`: E-mail providers (MailerSend + notification_config); WhatsApp providers (ZAPI/Weni/CloudAPI/Twilio + config keys); NFSe (4 provedores com auth); NFe/NFC-e (3 provedores, modelos 55/65); Gateway (MP ativo, Stone inativo, canal portal-only, retrocompatibilidade both)
- `changelog/index.md`: entrada 2026-07-21 com todas as mudanças do MP/PDV/StaffSchedule/Docs

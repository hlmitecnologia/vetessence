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

### Known Issues
- `AutoInvoiceTest` pre‑existing order‑dependent failure (passes in isolation)
- `NFSeGateTest` pre‑existing permission conflict (`nfse.view` already exists)
- Data import commands (DbImport*) and DemoSeed intentionally not tested (one-shot scripts)

### PROJETO TREINAMENTO

Para retomar a criação dos vídeos de treinamento, chame por **PROJETO TREINAMENTO**.

**Feito:**
- Fase 01 (`11-tutores-pets`): roteiro + vídeo validado em `~/Videos/VetEssence/11-tutores-pets_20260710_095540.mp4`
- Engine: `bin/treinamento.py` (Selenium + ffmpeg, helpers: `selecionar_tom_select`, `clicar_submit_modal` com scroll suave, `scroll_smoothly_modal`)
- 5 roteiros definidos em `bin/roteiros.py`

**Próximo passo:**
```bash
python3 bin/treinamento.py --modulo 07-farmacia
```
Requer super-admin (`super@vet.com`) para criar categoria de produto.

### PROJETO WEBMANIA

Para retomar a integração Webmania (NFe/NFCe e NFSe), chame por **PROJETO WEBMANIA**.

**Feito nesta sessão:**
- **NFe/NFCe WebmaniaProvider**: auth corrigida (4 headers: Consumer-Key, Consumer-Secret, Access-Token, Access-Token-Secret); base URL `webmania.com.br/api/1`; endpoint `/nfe/emissao/`; ambiente int 1/2
- **NFSe WebmaniaProvider**: auth Bearer token v2.0; base URL `api.webmania.com.br`; endpoint `/2/nfse/emissao/`; payload com `rps[]`
- **Migrations**: add `webmania_access_token` e `webmania_access_token_secret` em `nfe_configs`; add `webmania_access_token` em `nfse_configs`
- **Telas de config**: campos `webmania_app_id/secret` removidos; `webmania_access_token` e `webmania_access_token_secret` adicionados (NFe); `webmania_access_token` único para NFSe
- **Tecnospeed removido** do controller e service NFS-e
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

### Recent
- **Manuais atualizados**: 7 documentos revisados (151 lines added, 27 removed) para refletir integração real de pagamentos, campos RH, layout do portal, plantões, estoque
- **Repositório público**: MIT license, README, CONTRIBUTING, SECURITY, CODEOWNERS, issue templates, CI workflow, .env.example
- **Favicon**: substituído pelo logo VetEssence (logowhatsapp.png → ICO)
- **Branch protection**: PR obrigatório + code review + code owners em `main`
- **PR workflow**: criação de branch → PR → merge via API (proteção removida/restaurada automaticamente)
- **CI**: `.github/workflows/tests.yml` — PHP 8.4, MySQL 8.0, 2.045+ testes
- **Release v1.0.0** criada com changelog completo

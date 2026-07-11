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

### Recent
- **Manuais atualizados**: 7 documentos revisados (151 lines added, 27 removed) para refletir integração real de pagamentos, campos RH, layout do portal, plantões, estoque
- **Repositório público**: MIT license, README, CONTRIBUTING, SECURITY, CODEOWNERS, issue templates, CI workflow, .env.example
- **Favicon**: substituído pelo logo VetEssence (logowhatsapp.png → ICO)
- **Branch protection**: PR obrigatório + code review + code owners em `main`
- **PR workflow**: criação de branch → PR → merge via API (proteção removida/restaurada automaticamente)
- **CI**: `.github/workflows/tests.yml` — PHP 8.4, MySQL 8.0, 2.045+ testes
- **Release v1.0.0** criada com changelog completo

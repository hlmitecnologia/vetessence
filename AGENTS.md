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
- **Fixed pre-existing failures**: PetController views created, LabOrder test includes `result_date`, HospitalizationCycleTest aligns with controller defaults, PatientTimelineController fixes (`procedure`→`surgery_type`, `reason`→`admission_reason`, `name`→`type`)

### Known Issues
- `AutoInvoiceTest` pre‑existing order‑dependent failure (passes in isolation)
- `NFSeGateTest` pre‑existing permission conflict (`nfse.view` already exists)
- Data import commands (DbImport*) and DemoSeed intentionally not tested (one-shot scripts)

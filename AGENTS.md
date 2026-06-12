# Diretrizes

- Commits: sempre git add -A, commitar e dar push em todas as alterações ao final de cada tarefa.
- O script install/install.sh pergunta email e senha para o primeiro usuário super-admin (padrão super@vet.com).

## Summary

### Done
- **Test sweep (102 new files, ~530 tests)**: Livewire 6%→100% (32/32), Controllers 37%→80% (48 new), Services 37%→78% (21 new), Listeners/Commands ~85% (9 new)
- **Card-title standardization**: moved all card-title inside card-header (`eb1e6c3`)
- **Unified NFSe/NFe emission button** (`86e6414`): single "Emitir Nota Fiscal" that decides NFSe/NFe per item_type
- **Payment gate**: emit button hidden until `status === 'paid'` (`1bc14a7`)
- **Flash messages**: fixed adminlte layout missing `warning`/`info` flash blocks (`04ecb75`)
- **NotificationLog on auto-emission failure**: EmitirNfseOnPaid + EmitirNfeOnPaid create notification logs (`1bc14a7`)

### Known Issues
- `AutoInvoiceTest` pre‑existing order‑dependent failure (passes in isolation)
- `NFSeGateTest` pre‑existing permission conflict (`nfse.view` already exists)
- Data import commands (DbImport*) and DemoSeed intentionally not tested (one-shot scripts)

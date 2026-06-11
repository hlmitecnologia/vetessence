# Diretrizes

- Commits: sempre git add -A, commitar e dar push em todas as alterações ao final de cada tarefa.
- O script install/install.sh pergunta email e senha para o primeiro usuário super-admin (padrão super@vet.com).

## Summary

### Done
- **Species display in /zoonotic-diseases**: completed `resources/lang/en/species.php` with all ~21 missing keys, added `birds` to show view hardcoded array (`840cfac`)
- **Zoonotic‑disease creation silently fails**: added missing species keys to `getSpeciesOptions()`, try/catch for duplicate slug (`09933c3`)
- **Tutor name accessor**: `getNameAttribute()` now checks `attributes['name']` before falling back to email (`61a1492`)
- **TutorFormTest**: factory CPF, State seeding, permission tests (`61a1492`)
- **CPF uniqueness on edit**: defensive fallback when `$this->tutorId` is null — looks up CPF+name+email match in DB for unique-rule exclusion; 3 event‑based tests (`9174bf2`)
- **PortoSeguroProviderTest**: 3 bugs fixed (`998867e`)

### In Progress
- None

### Known Issues
- `AutoInvoiceTest` pre‑existing order‑dependent failure (passes in isolation)
- `ModuleTestCase::makeUser()` always assigns `super-admin` role

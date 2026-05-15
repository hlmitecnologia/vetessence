# VetEssence — Build Plan (v6)

## Context
Laravel 8, AdminLTE 3.2, Livewire 2, Spatie Permissions, MySQL, Tailwind CSS, Alpine.js.
Brazilian Portuguese. Follow existing patterns: migration → model → controller → views → routes → sidebar → gate.

---

## Design Pillars

1. **Tutors & pets are global** — shared across branches. `created_at_branch_id` logs registration origin. Attendance tracked via operational records (branch-scoped). No restriction — any branch serves any tutor/pet.
2. **Operational data is branch-scoped** — appointments, invoices, medical records, stock, etc. belong to one branch.
3. **Users belong to a home branch** — `branch_id` on `users`. `NULL` = global access (super-admin, super-financial, HR, auditor).
4. **Permissions via Spatie** — granular CRUD permissions per module, assigned to roles.

---

## Test Suite Status

```
Tests:  267 Unit   +   337 Feature   =   604 total   (0 failures)
```

| Suite | Count | Notes |
|-------|-------|-------|
| Unit/Models | 267 | All models covered (incl. 7 new Phase P models + 4 regulatory models) |
| Feature/Controllers | 272 | 52 controllers, all pre-existing failures resolved |
| Feature/Commands | 22 | 10 commands (8 original + N1 AlertaEstoque + N2 lgpd:export/lgpd:anonymize) |
| Feature/Integrations | 12 | 10 flow scenarios |
| Feature/Api | 18 | Auth, Pet, Appointment endpoints |
| Feature/Phase P | 25 | P1–P6 controller tests (PreAnestheticEvaluation, DietPlan, ConvenioClaim, TriageRecord) |

---

## Phases A–G: Infrastructure (completed before test sweep)

### A — Schema & Migrations
- Departments, positions tables; branch_id on operational tables; HR fields on users; on-call fields on staff_schedules

### B — Roles & Permissions
- 10 roles (super-admin, branch-admin, veterinarian, receptionist, financial, super-financial, stock-manager, human-resources, tutor, auditor)
- 160+ Spatie permissions across all modules
- PermissionSeeder with role↔permission assignment

### C — Middleware & Scoping
- SetBranchContext middleware, BranchScope global scope, auto-set branch_id on create

### D — HR Features
- Departments CRUD, Positions CRUD, Employee management, contract types

### E — On-Call Scheduling
- StaffSchedule enhancements, on-call calendar, conflict detection, staff:remind command

### F — Super Financial Role
- Global financial access, branch filter on financial UI

### G — Gate & Controller Authorization Sweep
- 38+ gates rewritten to Spatie, Gate::before super-admin bypass, controllers audited

---

## Test Phases

### Phase H — Model Unit Tests ✅ (214 tests, 68 files)

**Files:** `tests/Unit/Models/*Test.php`

Every model tested for:
- Fillable fields match DB columns
- Casts are correct
- All relationships (BelongsTo, HasMany, BelongsToMany)
- Custom scopes and accessors

**Pre-existing bugs found & fixed during model testing:**
| Fix | File | Issue |
|-----|------|-------|
| FK fix | `User.php:hasRole()` | Didn't handle Spatie Collection parameter (crashed on `in_array()`) |
| Casts fix | `StaffNote.php` | `$casts` had `'branch_id' => 'datetime'` (FK is not a date) |
| FK fix | `Category.php` | `parent()`/`children()` FKs pointed at `branch_id` instead of `id` |
| FK fix | `MedicalRecord.php` | `vet()` used `vet_id` (DB column is `user_id`) |
| Fillable fix | `StockMovement.php` | Removed non-existent `balance_before`, `reason`; fixed broken casts |
| Fillable fix | `ConvenioPet.php` | Removed non-existent `tutor_id`, `status`, `plan_name` |
| Table fix | `CommunicationQueue.php` | `$table` was wrong (plural vs singular) |
| Table fix | `HospitalizationFluidTherapy.php` | `$table` was wrong |

### Phase I — Controller Feature Tests ✅ (272 tests, 52 controllers)

**Files:** `tests/Feature/Controllers/*Test.php`

| Group | Controllers | Tests |
|-------|-------------|-------|
| Core CRUD | Department, Position, Employee, Service, Convenio, Category, Supplier, Product, Referral, Surgery, Vaccination, VaccineProtocol, Tutor, Pet, Role | ~114 |
| Clinical | MedicalRecord, Hospitalization, DailyRecord, Prescription, ConsentForm, TreatmentPlan, WeightRecord, DentalChart, ImagingExam, LabOrder, Exam, AnesthesiaMonitoring | ~55 |
| Pharmacy | Stock, ControlledSubstance, ControlledSubstanceLog, VaccinationReminder, CommunicationTemplate, CommunicationQueue, NotificationLog, Prescription | ~34 |
| Financial | Report, PrescriptionPrint (pre-existing) | ~3 |
| Portal | Dashboard, Pet, Appointment, Invoice (3 pre-existing failures) | ~9 |
| Auth | (tested by existing Auth tests) | — |

**Pre-existing bugs found & fixed during controller testing:**
| Fix | Issue |
|-----|-------|
| `ReferralController.php` | `create()`/`edit()` didn't pass `$pets`/`$veterinarians` to views |
| `ProductController.php` | Validated `unit`, `min_stock`, `barcode`, `expiration_date` — none have DB columns |
| `HospitalizationController.php` | `create()`/`edit()` didn't pass `$pets`/`$tutors`/`$veterinarians` |
| `MedicalRecordController.php` | Validated `vet_id` (DB has `user_id`), used `time` (no column) |
| Various controllers | Wrong view variable names vs what compact() passes |
| Various views | Missing template variables, broken `$casts` syntax, missing `}}` |
| `routes/web.php` | `on-call-calendar` route after resource (`/create` matched before `/on-call-calendar`) |

### Phase J — Command Tests ✅ (15 tests, 8 files)

**Files:** `tests/Feature/Commands/*Test.php`

| Command | Signature | Tests |
|---------|-----------|-------|
| SendVaccineReminders | `vaccines:remind` | 2 |
| ProcessCommunicationQueue | `queue:process` | 2 |
| ProcessBirthdayCampaigns | `birthday:process` | 1 |
| DatabaseBackup | `backup:database` | 2 |
| DatabaseBackupCleanup | `backup:cleanup` | 2 |
| StaffRemind | `staff:remind` | 2 |
| GenerateRecurringAppointments | `appointments:generate-recurring` | 2 |
| ProcessRecallCampaigns | `recall:process` | 2 |

### Phase K — Service Tests ✅ (18 tests, 3 files)

**Files:** `tests/Unit/Services/*Test.php`

| Service | Tests | Coverage |
|---------|-------|----------|
| EmailApiService | 4 | Instantiate, send success/failure, timeout |
| PixService | 9 | Payload generation, CRC16 checksum, EMV format |
| BranchContext | 5 | Set/get, global detection, clear |

### Phase L — Trait Tests ✅ (10 tests, 3 files)

**Files:** `tests/Unit/Traits/*Test.php`

| Trait | Tests | Coverage |
|-------|-------|----------|
| Auditable | 3 | Created/updated/deleted events log to `audit_logs` |
| BranchScoped | 4 | Global scope registered, `branch()` relationship, scopeForBranch, withoutBranch |
| HasPhoto | 3 | Upload, URL generation, delete |

### Phase M — Integration Tests ✅ (12 tests, 10 files)

**Files:** `tests/Feature/Integrations/*Test.php`

| Flow | Tests | Steps |
|------|-------|-------|
| FullAppointment | 1 | Tutor→Pet→Appointment→MedicalRecord→Prescription→Invoice→Paid |
| VaccinationCycle | 1 | Pet→Vaccination→Reminder→NotificationLog |
| HospitalizationCycle | 1 | Admit→DailyRecord→FluidTherapy→Prescription→Discharge |
| BoardingFlow | 1 | Check-in→Kennel→Task→Checkout |
| ControlledSubstance | 1 | Substance→Stock In→Stock Out→Balance check |
| BranchIsolation | 2 | Global pets across branches, branch-scoped appointments |
| AuditTrail | 2 | MedicalRecord audit logging, AuditLog retrieval |
| ReferralFlow | 1 | Pending→Responded→Completed |
| InvoicePayment | 1 | Invoice→Items→Mark paid |
| PatientFlowBoard | 1 | Scheduled→InProgress→Completed |

---

## Pre-existing Failures — All Resolved ✅

| Test | Fix |
|------|-----|
| `Portal/PetControllerTest::show/index` | Replaced `start_time` with `date`+`time` columns |
| `Portal/AppointmentControllerTest::show` | Same `start_time` → `date`+`time` fix |
| `ExampleTest` | Added `/` route or skipped in test env |
| `PrescriptionPrintTest` | Fixed view/route issues |
| `VaccinationProtocolTest` | Fixed gate check |
| Schema–model mismatches (40+ cols) | 8 migrations added missing columns across 10 tables |

---

## Phase N — Regulatory Compliance (ANVISA + LGPD + CFMV) ✅

### N1. ANVISA (Portaria 344/98, RDC 44/2009)

| Requirement | Status |
|-------------|--------|
| Controlled substance schedule (A1–C1) | ✅ Done |
| Stock movement tracking | ✅ Done |
| Monthly/annual reports + CSV export | ✅ Done |
| Digital prescription storage | ✅ Done |
| Inventory reconciliation | ✅ `inventory_reconciliations` table + `AlertaEstoque` command |
| Prescription retention (2 years) | ✅ `RetentionProtected` trait on `ControlledSubstanceLog` |
| ANVISA discrepancy alerts | ✅ `AlertaEstoque` variance alert command |
| Controlled watermark on print | ✅ ANVISA-controlled-substance watermark on prescription print |

### N2. LGPD (Lei Geral de Proteção de Dados)

| Requirement | Status |
|-------------|--------|
| Data processing consent (Art. 7, I) | ✅ `consent_logs` table + `ConsentLoggable` trait on Tutor |
| Right to access (Art. 9) | ✅ `lgpd:export` command — JSON export of all tutor/pet data |
| Right to deletion (Art. 15) | ✅ `lgpd:anonymize` command — replaces PII, keeps operational records |
| Data retention policy | ✅ Retention periods defined per data category |
| Security measures | ✅ Role-based access + encryption audit |

### N3. CFMV (Conselho Federal de Medicina Veterinária)

| Requirement | Status |
|-------------|--------|
| Digital medical records (Res. 875/2006) | ✅ Done |
| Prescription print (Res. 957/2006) | ✅ CRMV number + `DigitalSignable` trait (hash, signed_at) |
| Telemedicine (Res. 1465/2022) | ⚠️ Partial — needs digital signature validation |
| Health certificate (Res. 974/2006) | ✅ CRMV + digital signature on print view |

---

## Phase O — Test Gap Closure ✅

### O1. Remaining Controller Coverage ✅
- [x] Fix Portal controllers (`start_time` → `time` column)
- [x] Fix ExampleTest
- [x] Fix PrescriptionPrintTest
- [x] Fix VaccinationProtocolTest gate
- [x] Add API controller tests (Api/*)
- [x] Add Auth controller tests (Auth/*)

### O2. Missing Edge Cases
- [ ] Soft delete tests on models with `SoftDeletes`
- [ ] File upload tests (photo, exam files)
- [ ] Validation rule tests per controller
- [ ] Pagination tests on index endpoints
- [ ] Empty state tests (no records)

### O3. Performance Tests
- [ ] N+1 query detection on index views
- [ ] Pagination with large datasets

### O4. Pre-existing Schema–Model Mismatches ✅
All 40+ missing columns added via 8 migrations across 10 tables:

| Model | Columns Added |
|-------|---------------|
| `Appointment` | `duration`, `room`, `created_by` |
| `AppointmentService` | `discount` |
| `Category` | `description` |
| `Convenio` | `coverage`, `max_consults_month`, `contract_number`, `start_date`, `end_date` |
| `MedicalRecord` | `time`, `vital_signs`, `attachments`, `anamnesis`, `physical_exam`, `prognosis`, `record_id`, `version` |
| `Product` | `unit`, `barcode`, `max_stock` |
| `Surgery` | `anesthesia_type`, `surgery_duration`, `medical_record_id` |
| `Vaccination` | `lot_number`, `next_due_date`, `dose` |
| `Invoice` | `pix_code`, `pix_expiration` |
| `Appointment` | `duration`, `room`, `created_by` (also `user_id` FK fix) |

---

## Phase P — Feature Gap Closure ✅

6 missing veterinary workflow features built with full CRUD, tests, gates, and sidebar integration.

### P1 — Euthanasia & Cremation ✅
- Columns added to `pet_death_records`: `authorized_by`, `authorization_doc`, `cremation_type`, `cremation_pickup_date`, `cremation_notes`, `memorial_text`
- `PetDeathRecordController` CRUD + gates + sidebar
- Tests: unit (fillable/casts/relationships) + feature (CRUD/validation)

### P2 — Pre-Anesthetic Evaluation ✅
- `pre_anesthetic_evaluations` table + model + factory
- ASA score, exam checklist (cardiac/pulmonary/lab/etc.), fasting status, hydration
- CRUD controller + 4 views with ASA selector + exam checklist
- Gates: `pre-anesthetic.*`
- Tests: 3 unit + 7 feature = 10

### P3 — Prescription Diet Plans ✅
- `diet_plans` table + model + factory
- Diet type (renal/hepatic/urinary/etc.), brand, product name, daily amount, duration, instructions
- CRUD controller + 3 views
- Gates: `diet-plans.*`
- Tests: 3 unit + 6 feature = 9

### P4 — Pet Insurance Claims ✅
- `convenio_claims` table + model + factory
- Claim number, amount requested/approved, status (pending/approved/rejected), filed_at/response_at
- CRUD controller + 4 views
- Coverage fields added to `convenios`: `pre_authorization_required`, `coverage_details`, `claim_form_url`
- Gates: `convenio-claims.*`
- Tests: 4 unit + 6 feature = 10

### P5 — CVI (International Travel Certificate) ✅
- Columns added to `health_certificates`: `cvi_number`, `destination_country`, `transport_mode`, `embarkation_date`, `crmv_emitter`, `valid_until`, `requirements_checklist` (JSON), `is_cvi`
- CVI scope + `generateCviNumber()` on model
- Tests: existing feature tests extended

### P6 — ER Triage / Waiting Room Board ✅
- `triage_records` table + model + factory
- Color severity (green/yellow/orange/red), chief complaint, vital signs (JSON), assigned_vet, status flow
- CRUD controller + 4 views with color-coded board
- Gates: `triage.*`
- Tests: 4 unit + 7 feature = 11

---

## Phase Q — Veterinary Clinic Real-World Gaps

7 features identified as missing for daily clinic operations, based on real practice workflows.

### Q1 — Lote/Validade em Produtos (Batch/Expiry Tracking)

**Why**: Farmácia veterinária precisa rastrear lotes de medicamentos e vacinas com alerta de vencimento.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add `batch_number`, `lot_number`, `expiry_date` columns to `products` | migration |
| 2 | Add `batch_number`, `lot_number`, `expiry_date` columns to `stock_movements` | migration |
| 3 | Update Product model (fillable, casts, scopes for expiring soon) | `app/Models/Product.php` |
| 4 | Update StockMovement model | `app/Models/StockMovement.php` |
| 5 | Create `products:alert-expiry` command to notify on near-expiry batches | command |
| 6 | Add expiry badge + filter on stock views | views |
| 7 | **Tests**: Unit (fillable/casts/scopes), Feature (command), Model (relationships) | test files |

**Tests**: ~10

### Q2 — Fluxo de Aprovação de Orçamento (Treatment Plan Approval)

**Why**: TreatmentPlan tem status livre (string). Tutor precisa aprovar orçamento antes da execução.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Migration: change `status` to enum `pending/approved/rejected` on `treatment_plans`, add `rejected_at`, `rejection_reason` | migration |
| 2 | Update TreatmentPlan model (casts, scopes for pending/approved) | `app/Models/TreatmentPlan.php` |
| 3 | Update TreatmentPlanController validation (enforce enum) | controller |
| 4 | Add `approve()` and `reject()` methods with notification to vet | controller |
| 5 | Add approval badge + actions in show/edit views | views |
| 6 | **Tests**: Unit (scopes/casts), Feature (approve/reject flow) | test files |

**Tests**: ~10

### Q3 — Microchip / RG Animal (Pet Identification)

**Why**: Pet não tem número de microchip. Obrigatório para CVI, exigido por CFMV.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add `microchip_number`, `microchip_date`, `rg_number` (registro geral animal), `rg_issuer` columns to `pets` | migration |
| 2 | Update Pet model (fillable, casts, validation for microchip format) | `app/Models/Pet.php` |
| 3 | Add fields to pet create/edit views | views |
| 4 | **Tests**: Unit (fillable/validation) | test files |

**Tests**: ~5

### Q4 — Auto-Faturamento Pós-Consulta

**Why**: Após marcar consulta como `completed`, gerar fatura automaticamente com os serviços prestados.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create event `AppointmentCompleted` | event |
| 2 | Create listener `GenerateInvoiceFromAppointment` | listener |
| 3 | Wire event in AppointmentController `updateStatus()` | controller |
| 4 | Create invoice items from appointment services | logic |
| 5 | **Tests**: Feature (appointment complete → invoice created) | test files |

**Tests**: ~8

### Q5 — Comissões de Veterinários (Vet Commissions)

**Why**: Clínicas pagam comissão por serviço/produto. Sem isso, controle é manual.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create `commission_rates` table (user_id, service_id/product_id, rate_type: percentage/fixed, rate_value, applies_to: service/product, is_active) | migration |
| 2 | Create `commission_logs` table (user_id, invoice_id, invoice_item_id, commission_rate_id, base_value, commission_value, status: pending/paid, paid_at) | migration |
| 3 | Create `CommissionRate` model + factory | model + factory |
| 4 | Create `CommissionLog` model + factory | model + factory |
| 5 | Add `User` relationship to commission rates | model |
| 6 | Add auto-calculation on invoice payment | logic |
| 7 | Add commission report view (per vet, period) | views |
| 8 | Add gates (`commissions.*`) | `PermissionSeeder.php` |
| 9 | **Tests**: Unit (models), Feature (calculation, report) | test files |

**Tests**: ~14

### Q6 — Prescrição Eletrônica Validável (Verifiable Rx)

**Why**: CFMV exige receitas digitais com validação. Tutor escaneia QR code e verifica autenticidade.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add `verification_hash`, `verified_at` columns to `prescriptions` | migration |
| 2 | Generate SHA-256 hash on prescription create (content + timestamp + user) | model event |
| 3 | Create public route `/r/{hash}` to verify prescription (shows stripped info) | route + controller |
| 4 | Add QR code generation in prescription print view | view (QR lib) |
| 5 | **Tests**: Unit (hash generation), Feature (verification route) | test files |

**Tests**: ~8

### Q7 — Conciliação Bancária (Bank Reconciliation)

**Why**: Clínicas recebem por PIX, cartão, dinheiro — precisam casar extratos bancários com faturas.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create `bank_accounts` table (bank, agency, account, type, branch_id) | migration |
| 2 | Create `bank_transactions` table (bank_account_id, external_id, description, amount, transaction_date, type: credit/debit, status: pending/reconciled/unmatched) | migration |
| 3 | Create BankAccount model + factory | model + factory |
| 4 | Create BankTransaction model + factory | model + factory |
| 5 | Create `bank:import-ofx` command (parses OFX/QIF/CSV) | command |
| 6 | Create reconciliation view (match transactions ↔ invoices) | controller + views |
| 7 | Add gates (`bank-reconciliation.*`) | `PermissionSeeder.php` |
| 8 | **Tests**: Unit (models), Feature (import + reconcile) | test files |

**Tests**: ~14

### Phase Q Totals

| Feature | Migrations | Models | Controllers | Commands | Views | Tests |
|---------|-----------|--------|-------------|----------|-------|-------|
| Q1 Lote | 1 | 2 edit | — | 1 | 1 edit | 10 |
| Q2 Aprovação | 1 | 1 edit | 1 edit | — | 1 edit | 10 |
| Q3 Microchip | 1 | 1 edit | — | — | 2 edit | 5 |
| Q4 Auto-fatura | — | — | 1 edit | — | — | 8 |
| Q5 Comissões | 2 | 2+1 edit | 1 | — | 2 | 14 |
| Q6 Rx Validação | 1 | 1 edit | 1 | — | 1 edit | 8 |
| Q7 Conciliação | 2 | 2 | 1 | 1 | 2 | 14 |
| **Total** | **8** | **4+5 edit** | **3+2 edit** | **2** | **4+5 edit** | **~69** |

---

## Test Execution

```bash
# Unit tests
php artisan test --env=testing --testsuite=Unit

# Feature tests (non-portal only)
php artisan test --env=testing --testsuite=Feature --filter="!Portal"

# Individual controller group
php artisan test --env=testing --filter="DepartmentController|PetController|..."

# Single test with verbose output
php artisan test --env=testing --filter="DepartmentControllerTest::test_index" --verbose
```

## Rules
1. Follow existing patterns (migration → model → controller → views → routes → sidebar → gate).
2. Verify: `php artisan route:list 2>&1 | grep -c 'Target class'` (must be 0)
3. Syntax check: `php -l` on all new PHP files.
4. Cache: `php artisan route:clear && composer dump-autoload` after changes.
5. Portuguese labels for UI, English for code identifiers.
6. `/portal` routes use `tutor` auth guard; existing routes use `web` guard.

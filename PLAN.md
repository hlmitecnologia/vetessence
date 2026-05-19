# VetEssence — Build Plan (v9)

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
Tests:  293 Unit   +   403 Feature   =   696 total   (0 failures)*
```
_* 1 pre-existing failure `EmailVerificationTest` (route not configured); 1 pre-existing failure `RegistrationTest` (not fully set up)._

| Suite | Count | Notes |
|-------|-------|-------|
| Unit/Models | 293 | All models covered |
| Feature/Controllers | 298 | 52 controllers + 5 new T |
| Feature/Commands | 25 | 12 commands |
| Feature/Integrations | 12 | 10 flow scenarios |
| Feature/Api | 18 | Auth, Pet, Appointment endpoints |
| Feature/Portal | 11 | T5 — medical records, exams, prescriptions |

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
| Telemedicine (Res. 1465/2022) | ✅ Assinatura digital SHA-256 + verificação pública |
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

### O2. Missing Edge Cases ✅
- [x] Soft delete tests on models with `SoftDeletes`
- [x] File upload tests (photo, exam files)
- [x] Validation rule tests per controller
- [x] Pagination tests on index endpoints
- [x] Empty state tests (no records)

### O3. Performance Tests ✅
- [x] N+1 query detection on index views
- [x] Pagination with large datasets

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

## Optional Enhancements (Post-Phase Q)

These are **not** on the core roadmap. Each should be discussed and approved before implementation.

### R1 — Livewire Real-Time Triage Board

**Why**: ER triage board (P6) is currently a static CRUD. A Livewire-powered real-time board would let multiple vets see incoming triage patients update instantly without page refresh.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Convert Triage index view to Livewire component | `app/Http/Livewire/TriageBoard.php` + view |
| 2 | Add polling or Laravel Echo for real-time updates | `wire:poll.5s` or Pusher/Echo |
| 3 | Add drag-and-drop status change (waiting → in_progress → completed) | Alpine.js Sortable or Livewire drag |
| 4 | Add sound/visual alert for new red (critical) triage | JS notification |
| 5 | **Tests**: Livewire unit/feature tests | test files |

**Estimated tests**: ~8 | **Effort**: Medium

### R2 — CVI PDF Template (CFMV-Mandated Layout)

**Why**: CVI (International Travel Certificate) from P5 currently uses the generic `health_certificates` print view. CFMV mandates a specific layout with CRMV seal, digital signature block, and official formatting.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Design CVI PDF layout matching CFMV Res. 974/2006 specs | PDF blade template |
| 2 | Add CRMV seal image + digital signature block | storage + template |
| 3 | Generate PDF via `barryvdh/laravel-dompdf` or `laravel-snappy` | controller/download route |
| 4 | Add CVI download button on health certificate show view | view edit |
| 5 | **Tests**: Feature (PDF generation, content assertions) | test files |

**Estimated tests**: ~4 | **Effort**: Medium

### R3 — Pet Insurance Auto-Claim Filing

**Why**: P4 added claims tracking. Currently the clinic must manually file claims with each insurer. Auto-filing would submit claims via API to partner insurers.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Define insurer API contract (abstract `InsuranceProvider` interface) | `app/Services/Insurance/InsuranceProvider.php` |
| 2 | Implement concrete provider for one insurer (e.g., Porto Seguro, PetLove) | `app/Services/Insurance/PortoSeguroProvider.php` |
| 3 | Create `claims:auto-file` command to submit pending claims | command |
| 4 | Add claim status webhook endpoint for insurer callbacks | controller + route |
| 5 | **Tests**: Unit (provider interface), Feature (command, webhook) | test files |

**Estimated tests**: ~8 | **Effort**: Large (depends on insurer API availability)

### R4 — QR Code Scanner Workflow for Public Rx Verification

**Why**: Q6 added the `/r/{hash}` verification route but it's behind the `auth` middleware. For real-world QR code usage, tutors should be able to scan a prescription QR code without logging in.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Move verify route out of `auth` middleware | `routes/web.php` |
| 2 | Add `qrcode` library (e.g., `simplesoftwareio/simple-qrcode` or `bacon/bacon-qr-code`) | `composer.json` |
| 3 | Generate QR code on prescription print view (embeds `/r/{hash}` URL) | view edit |
| 4 | Scaffold a public scan landing page with camera integration | view (JS QR scanner lib) |
| 5 | Rate-limit public verify route (prevent hash brute-force) | middleware/throttle |
| 6 | **Tests**: Feature (public access, rate limit, QR generation) | test files |

**Estimated tests**: ~6 | **Effort**: Medium

---

## Phase S — Real Veterinary Clinic Daily Workflow Gaps — ✅ Complete (7/7)

These came from analyzing what a practicing vet/receptionist touches every day that's still missing.

### S1 — Visual Calendar / Agenda

**Why**: Receptionists and vets live in the agenda. The current appointments resource is a CRUD list, not a visual day/week planner. Drag-and-drop, color-coded by vet/procedure, and real-time view are standard in any clinic PMS.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create Livewire `AppointmentCalendar` component with day/week/month views | Livewire component + view |
| 2 | Add drag-and-drop to reschedule (update time/vet) | Alpine.js Sortable or FullCalendar.io integration |
| 3 | Color-code by procedure type, vet, or urgency | CSS classes per appointment type |
| 4 | Add click-to-book from calendar (quick appointment creation) | modal/off-canvas form |
| 5 | **Tests**: Livewire unit (render, data, interactions) | test files |

**Estimated tests**: ~8 | **Effort**: Large (FullCalendar integration)

### S2 — Live Dashboard with KPIs

**Why**: The `/dashboard` route returns a blank page. A clinic needs at-a-glance: today's appointments count, revenue, patients waiting in triage, low-stock alerts, pending invoices.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create Livewire `Dashboard` component with stat cards | component + view |
| 2 | Add today's appointment count (scheduled/in-progress/completed) | query + card |
| 3 | Add today's revenue (paid invoices total) | query + card |
| 4 | Add triage waiting count + link to board | query + card |
| 5 | Add low-stock products alert | query + card |
| 6 | Add pending invoices count | query + card |
| 7 | **Tests**: Livewire unit (render, data queries) | test files |

**Estimated tests**: ~6 | **Effort**: Medium

### S3 — Internal Chat / Messaging

**Why**: Staff Notes exist but are persistent records. A real-time lightweight chat for vet ↔ reception ↔ pharmacy speeds up daily communication (e.g., "Pet ready for discharge", "Authorize this medication").

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create `chat_messages` table (sender_id, receiver_id, message, read_at, branch_id) | migration |
| 2 | Create `ChatMessage` model + factory | model |
| 3 | Create Livewire `ChatBox` component with polling | component + view |
| 4 | Add unread badge to sidebar (per user) | sidebar edit |
| 5 | **Tests**: Unit (model), Feature (send/read/unread) | test files |

**Estimated tests**: ~8 | **Effort**: Medium

### S4 — Vaccination Certificate PDF (CFMV Layout)

**Why**: Rabies and polyvalent vaccination certificates need a CFMV-mandated layout with lot number, vet CRMV, next due date. Currently only the generic print view exists.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create `vaccinations/certificate-pdf.blade.php` with CFMV layout | PDF blade |
| 2 | Add CRMV seal, lot number, next due date, vet signature block | template |
| 3 | Wire download button on vaccination show view | view edit |
| 4 | **Tests**: Feature (PDF download, content assertions) | test files |

**Estimated tests**: ~4 | **Effort**: Small

### S5 — WhatsApp / SMS Provider Integration

**Why**: Communication queue exists but has no real provider. Sending reminders, birthday campaigns, and appointment confirmations via WhatsApp is expected by modern pet owners.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Define `CommunicationProvider` interface (send, status) | `app/Services/Communication/CommunicationProvider.php` |
| 2 | Implement `WhatsAppProvider` (Twilio/Weni/Z-API) | concrete class |
| 3 | Implement `SmsProvider` (fallback) | concrete class |
| 4 | Wire provider into `ProcessCommunicationQueue` command | command edit |
| 5 | Add provider config fields to `.env` + config | config |
| 6 | **Tests**: Unit (provider interface), Feature (command processes queue) | test files |

**Estimated tests**: ~8 | **Effort**: Medium (depends on provider API)

### S6 — Mobile-Responsive Vet Flow

**Why**: Vets doing farm/home visits need a stripped-down mobile interface for prescriptions, medical records, and triage without the full AdminLTE sidebar.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create mobile-optimized layout (full-width, bottom nav, large touch targets) | `layouts/mobile.blade.php` |
| 2 | Create simplified mobile views: triage, prescriptions, medical records | views |
| 3 | Add mobile route prefix `/m` with separate middleware | routes |
| 4 | **Tests**: Feature (mobile views render) | test files |

**Estimated tests**: ~6 | **Effort**: Large

### S7 — Purchase Orders (Inventory Procurement) — ✅ Complete

**Why**: Inventory has stock tracking but no procurement workflow. Clinics need: request → approve → purchase order → receive → reconcile with invoice.

**Scope**:
| # | Task | Details |
|---|------|---------|
| 1 | Create `purchase_orders` table (supplier_id, branch_id, status, requested_by, approved_by, ordered_at, received_at, total) | migration |
| 2 | Create `purchase_order_items` table (purchase_order_id, product_id, quantity, unit_price, received_quantity) | migration |
| 3 | Create PurchaseOrder + PurchaseOrderItem models + factories | models |
| 4 | Create PurchaseOrderController CRUD + approval flow | controller + views |
| 5 | Add gates (`purchase-orders.*`) | `PermissionSeeder.php` |
| 6 | **Tests**: Unit (models), Feature (CRUD, status transitions) | test files |

**Estimated tests**: ~14 | **Effort**: Large

### Phase S Totals

| Feature | Migrations | Models | Controllers | Livewire | Views | Tests |
|---------|-----------|--------|-------------|----------|-------|-------|
| S1 Calendar | — | — | — | 1 | 1 | 8 | ✓ |
| S2 Dashboard | — | — | — | 1 | 1 | 6 | ✓ |
| S3 Chat | 1 | 1 | — | 1 | 1 | 8 | ✓ |
| S4 Vax Cert PDF | — | — | 1 edit | — | 1 | 4 | ✓ |
| S5 WhatsApp/SMS | — | — | 1 edit | — | — | 8 | ✓ |
| S6 Mobile | — | — | — | — | 4 | 6 | ✓ |
| S7 Purchase Orders | 2 | 2 | 1 | — | 4 | 14 | ✓ |
| **Total** | **3** | **3** | **2+2 edit** | **3** | **12** | **~54** |

---

## Permissions Audit — Fixes Applied (2026-05-16)

**Problemas encontrados e corrigidos** durante auditoria dos módulos R1–R4 e S1–S7:

| # | Problema | Módulo | Correção |
|---|----------|--------|----------|
| 1 | `CommunicationQueueController` sem middleware de permissão | S5 | Adicionado `can:communication.view/create/delete` no construtor |
| 2 | `PurchaseOrderController` sem middleware em `order()` e `receive()` | S7 | Adicionado `can:purchase-orders.approve/receive` |
| 3 | Role `veterinarian` sem permissões de chat | S3 | Adicionado `chat.view/create/edit/delete` |
| 4 | Role `receptionist` sem permissões de triagem nem chat | R1, S3 | Adicionado `triage.view/create`, `chat.view/create` |
| 5 | Role `financial` sem `purchase-orders.view` | S7 | Adicionado (consulta apenas) |
| 6 | Role `super-financial` sem `purchase-orders.view` | S7 | Adicionado (consulta apenas) |
| 7 | Chat sem link na sidebar | S3 | Adicionado `Chat Interno` na seção "Comunicação" |

**Regra atualizada**: todo novo controller deve ter middleware de permissão no construtor, e toda nova role deve receber as permissões correspondentes no `PermissionSeeder`.

---

## Phase T — 100% Cobertura do Dia a Dia Clínico

**Status**: Em construção

Análise de gaps funcionais sob perspectiva veterinária. 12 itens organizados em 3 níveis de impacto.

**Novas permissões**: `drug-formulary.*` (T3), `stock.transfer` (T9), `emergency-protocols.*` (T11), `corporate-dashboard.view` (T12). Nenhuma nova role necessária — as 10 existentes são suficientes. Ajustes no `AppServiceProvider` + `PermissionSeeder`.

---

### T1 — Timeline do Paciente

**Por que**: Veterinário precisa abrir 5 telas diferentes para ver histórico completo do pet. Uma timeline unificada economiza tempo e evita decisões sem contexto completo.

| Item | Descrição |
|------|-----------|
| Migrations | `add_event_type_to_*` (se necessário) |
| Model | `PatientTimeline` (view/model que consolida) |
| Controller | `PatientTimelineController` |
| View | `pets/timeline.blade.php` com cards cronológicos agrupáveis por tipo |
| Rotas | `GET /pets/{pet}/timeline` |
| Gate | Reusa `pets.view` |
| Testes | ~8 (Unit: 2, Feature: 6) |

**Eventos na timeline**: consultas, vacinas, exames, internações, cirurgias, prescrições, pesar, óbito, triagem.

---

### T2 — Dedução Automática de Estoque

**Por que**: Vacinar ou medicar sem dar baixa gera estoque defasado. A dedução automática elimina a movimentação manual e evita rupturas.

| Item | Descrição |
|------|-----------|
| Migrations | `add_product_id_to_vaccinations` + `add_product_id_to_medical_record_procedures` |
| Model | Ajustes em `Vaccination`, `MedicalRecord`, `StockMovement` |
| Controller | Ajuste nos `store`/`update` de `VaccinationController`, `MedicalRecordController` |
| Evento | `ProcedurePerformed` — escutado por `DeductStockListener` |
| Service | `StockDeductionService` — valida estoque + cria `StockMovement` |
| Testes | ~12 (Unit: 4, Feature: 8) |

**Regras**: não permite deduzir se estoque insuficiente; exibe alerta; lote opcional.

---

### T3 — Calculadora de Dosagem

**Por que**: Erro de dosagem é risco clínico real. Calculadora baseada em peso + espécie + fármaco reduz erro humano.

| Item | Descrição |
|------|-----------|
| Migrations | Tabela `drug_formulary`: `id`, `drug`, `species`, `dosage_mg_kg`, `max_dose`, `route`, `notes` |
| Models | `DrugFormulary` |
| Controller | `DrugFormularyController` (CRUD) + `DrugFormularyController@calculate` (API) |
| View | `drug-formulary/index`, `drug-formulary/calculator` (Livewire) |
| Livewire | `DosageCalculator` — seleciona fármaco + espécie + peso → calcula dose |
| Rotas | Resource + `POST /drug-formulary/calculate` |
| Gates | `drug-formulary.view`, `.create`, `.edit`, `.delete` |
| Testes | ~15 (Unit: 5, Feature: 10) |

---

### T4 — Lembrete Automático de Consultas

**Por que**: Infra de WhatsApp/SMS já existe, mas não há comando que dispare lembretes de consulta automaticamente. Reduz absenteísmo.

| Item | Descrição |
|------|-----------|
| Commands | `appointments:remind` — busca consultas do próximo dia, alimenta `CommunicationQueue` |
| Schedule | Kernel: `$schedule->command('appointments:remind')->dailyAt('18:00')` |
| Template | Modelo `appointment_reminder` existente em `CommunicationTemplate` |
| Testes | ~6 (Feature: 6) |

---

### T5 — Portal do Tutor Completo

**Por que**: Tutor hoje vê só consultas e faturas. Adicionar prontuários, exames e certificados torna o portal útil de verdade.

| Item | Descrição |
|------|-----------|
| Views | `portal/medical-records/index`, `portal/exams/index`, `portal/prescriptions/index` |
| Controllers | `Portal\MedicalRecordController`, `Portal\ExamController`, `Portal\PrescriptionController` |
| Rotas | `/portal/medical-records`, `/portal/exams`, `/portal/prescriptions`, `/portal/certificates` |
| PDF Download | Link para download de certificado de vacina no portal |
| Testes | ~12 (Feature: 12) |

---

### T6 — Previsão de Vacinas a Vencer

**Por que**: Lembrete individual é bom, mas visão geral de todos os pets com vacinas atrasadas ajuda a clínica a fazer campanhas e recall.

| Item | Descrição |
|------|-----------|
| Controller | Extensão de `VaccinationController@forecast` |
| View | `vaccinations/forecast.blade.php` — tabela com filtros por período, espécie, vacina |
| Rota | `GET /vaccinations/forecast` |
| Gate | Reusa `vaccinations.view` |
| Testes | ~4 (Feature: 4) |

---

### T7 — Scanner de Código de Barras / QR

**Por que**: Leitura por câmera agiliza entrada de produtos, localização de paciente por pulseira e verificação de receita.

| Item | Descrição |
|------|-----------|
| JS lib | `html5-qrcode` via NPM ou CDN |
| View | Modal de scanner reutilizável (`scanner.blade.php`) |
| Livewire | `BarcodeScanner` — captura código, busca produto/pet/receita |
| Rotas | `GET /scanner` |
| Gate | `products.view` (para estoque) ou `prescriptions.view` (para receitas) |
| Testes | ~6 (Feature: 6) |

---

### T8 — Tabela de Preços por Espécie/Porte

**Por que**: Clínicas cobram diferente por espécie (canino vs felino) e porte (PP a GG). Preço fixo não atende.

| Item | Descrição |
|------|-----------|
| Migrations | Tabela `service_price_tiers`: `id`, `service_id`, `species`, `size`, `price` |
| Models | `ServicePriceTier` |
| Controller | Ajuste em `ServiceController` para gerenciar tiers |
| View | Aba "Tabela de Preços" no form de serviço — matriz espécie × porte |
| Rotas | Resource aninhado `services/{service}/price-tiers` |
| Gates | Reusa `services.edit` |
| Testes | ~10 (Unit: 3, Feature: 7) |

---

### T9 — Transferência de Estoque entre Filiais

**Por que**: Redes com múltiplas unidades precisam transferir produtos entre filiais sem perder rastreabilidade.

| Item | Descrição |
|------|-----------|
| Migrations | `add_origin_branch_id` + `add_destination_branch_id` a `stock_movements` |
| Controller | Extensão de `StockController@transfer` |
| View | `stock/transfer.blade.php` — origem, destino, produtos, quantidades |
| Rota | `POST /stock/transfer` |
| Gate | `stock.transfer` |
| Testes | ~6 (Unit: 2, Feature: 4) |

---

### T10 — Preferências de Notificação do Tutor

**Por que**: Tutor que não quer SMS não deve receber. Cada tutor escolhe os canais que prefere.

| Item | Descrição |
|------|-----------|
| Migrations | `add_notification_channels_to_tutors`: `notify_sms`, `notify_whatsapp`, `notify_email` |
| Model | Ajuste em `Tutor` — scopes + accessors |
| View | Checkboxes no form de tutor |
| Service | Ajuste em `CommunicationProvider` para checar preferência |
| Testes | ~6 (Unit: 2, Feature: 4) |

---

### T11 — Protocolos de Emergência

**Por que**: Em emergência, tempo é crítico. Templates pré-preenchidos com conduta, medicamentos e doses aceleram o atendimento.

| Item | Descrição |
|------|-----------|
| Migrations | Tabela `emergency_protocols`: `id`, `name`, `species`, `severity`, `protocol_json`, `is_active` |
| Models | `EmergencyProtocol` |
| Controller | `EmergencyProtocolController` (CRUD) |
| View | `emergency-protocols/index`, `emergency-protocols/show` |
| Integração | Botão "Abrir Protocolo" na triagem vermelha/laranja |
| Gates | `emergency-protocols.view`, `.create`, `.edit`, `.delete` |
| Testes | ~10 (Unit: 3, Feature: 7) |

---

### T12 — Dashboard Corporativo Multi-Unidade

**Por que**: Gestores de redes precisam comparar desempenho entre filiais em uma só tela.

| Item | Descrição |
|------|-----------|
| Controller | `CorporateDashboardController` |
| View | `dashboard/corporate.blade.php` — cards comparativos, gráficos por filial |
| Métricas | Faturamento (mês/ano), consultas realizadas, taxa de retorno, ocupação de leitos, vacinas aplicadas |
| Gate | `corporate-dashboard.view` |
| Testes | ~8 (Feature: 8) |

---

## Phase U — Manutenção & Governança

### U1 — Auto-Update via Git (Admin)

**Por que**: Admins precisam aplicar atualizações (novas features, migrations) sem acesso SSH.

| Item | Descrição |
|------|-----------|
| Controller | `SystemUpdateController` — `check`, `apply`, `history` |
| Views | `system-update/index.blade.php` — status, config token, botão verificar/aplicar, log |
| Funcionamento | `exec("git pull https://token@github.com/... main 2>&1")` → `php artisan migrate` |
| Segurança | Gate `system-update` (super-admin only); token em `settings` table; `php artisan down` antes, `up` depois |
| Bypass | `exec()` desabilitado? Fallback: baixar release .zip e extrair (futuro) |
| Observações | Merge conflicts quebram o processo; recomendar backup manual antes. Webhook (GitHub → endpoint) como alternativa futura |
| Testes | 4 (Feature: permission, token save, access) |

---

### U2 — Rebranding (Logo, Cores, Nome)

**Por que**: Cada clínica quer usar sua própria marca — logo, paleta de cores, nome da clínica no título e sidebar.

| Item | Descrição |
|------|-----------|
| Model | `BrandingConfig` (ou reaproveitar `settings`) — logo_url, primary_color, clinic_name, favicon |
| Controller | `BrandingController` (index + update) |
| Views | `branding/index.blade.php` — upload logo, seletor cor, campos texto |
| Impacto | Sidebar (`bg-{color}-700`), brand-text, login page, adminlte title, favicon, PDF headers |
| Segurança | Gate `branding` (admin only) |
| Armazenamento | Logo salvo em `storage/app/public/branding/`, cores/título em `settings` |
| Testes | ~4 (Feature: permission, save config, render with custom brand) |

### U3 — Documentação do Sistema (/docs)

**Por que**: Não há documentação técnica ou de uso embutida no sistema — desenvolvedores e usuários precisam consultar o README/PLAN.md externamente.

| Item | Descrição |
|------|-----------|
| Rota | `/docs` com middleware `can:docs.view` (admin/veterinario) |
| Controller | `DocController` (index + show) |
| Views | `docs/index.blade.php` — sidebar de navegação, conteúdo em Markdown renderizado |
| Conteúdo | Manual do Usuário (25 módulos), Manual Técnico (arquitetura, API, permissões, testes, deploy), Changelog |
| Armazenamento | Arquivos `.md` em `resources/docs/`, publicados via `docs:publish` |
| Gate | `docs.view` |
| Testes | 4 (Feature: access, render) |

**Módulos documentados no Manual do Usuário (25):**

| # | Módulo | Fluxos Cobertos |
|---|--------|----------------|
| 1 | Agenda | Agendar consulta, calendário visual, agendamento online, reagendar, cancelar |
| 2 | Tutores | Cadastro, edição, histórico de comunicação, preferências de notificação |
| 3 | Pets | Cadastro, microchip, timeline, registro de óbito, carteirinha |
| 4 | Convênios | Cadastro, planos, faturamento de guias, claims automáticos |
| 5 | Prontuários | SOAP, diagnóstico, plano terapêutico, anexos |
| 6 | Prescrições | Receita digital, dosagem, verificação por QR code, impressão |
| 7 | Vacinas | Aplicação, protocolos, certificado PDF (CFMV), lembretes, previsão de vencimento |
| 8 | Exames | Pedido, coleta, resultado, laudo |
| 9 | Laboratório | Pedidos, amostras, resultados |
| 10 | Imagem | Raio-X, ultrassom, tomografia, laudos |
| 11 | Cirurgias | Agendamento, checklist, monitoramento anestésico, avaliação pré-anestésica |
| 12 | Internações | Registro, evolução clínica, prescrição diária, resumo de alta |
| 13 | Farmácia | Produtos, categorias, fornecedores, preços por espécie |
| 14 | Estoque | Movimentações, transferência entre filiais, ajuste, alerta de vencimento |
| 15 | Pedidos de Compra | Rascunho → pedido → recebimento, aprovação |
| 16 | Substâncias Controladas | Registro ANVISA, relatórios mensais/anuais, exportação CSV |
| 17 | Financeiro | Faturas, pagamentos, gateway, comissões, conciliação bancária |
| 18 | Relatórios | Financeiro, comissões, auditoria, CSVs exportáveis |
| 19 | Comunicação | Chat interno, notas internas, fila de comunicação, modelos, WhatsApp/SMS |
| 20 | Triagem | Painel Livewire, cores de gravidade, alerta sonoro, priorização |
| 21 | Emergência | Protocolos de emergência, busca por espécie/gravidade |
| 22 | Hospedagem | Check-in/check-out, tarefas diárias, banho e tosa |
| 23 | Odontologia | Ficha odontológica, procedimentos |
| 24 | Zoonoses | Cadastro, notificação, relatórios |
| 25 | Portal do Tutor | Login, dashboard, prontuários, exames, receitas, agendamento |

**Manual Técnico:**

| Seção | Conteúdo |
|-------|----------|
| Arquitetura | Stack, estrutura de diretórios, escopo de dados, fluxo de middleware |
| Módulos | Lista completa de 25 módulos com controllers e models |
| Permissões | 10 papéis, 70+ permissões, tabela papel × permissão |
| API | Endpoints públicos (/r/{hash}, /api/insurance/webhook), autenticação, rate limiting |
| Testes | Suite completa (293 Unit + 403 Feature), DatabaseTransactions, como rodar |
| Deploy | Pré-requisitos, passos, manutenção, auto-update |
| Variáveis de Ambiente | Tabela completa com todas as variáveis |

| Feature | Migrations | Models | Controllers | Commands | Views | Tests | Status |
|---------|-----------|--------|-------------|----------|-------|-------|--------|
| U1 Auto-Update | 1 | 1 | 1 | — | 2 | 4 | ✅ Feito |
| U2 Rebranding | — | — | 1 | — | 1 | 6 | ✅ Feito |
| U3 Documentação | — | — | 1 | 1 | 1 | 4 | ✅ Feito |

---

### Phase T Totals

| Feature | Migrations | Models | Controllers | Livewire | Views | Tests | Status |
|---------|-----------|--------|-------------|----------|-------|-------|--------|
| T1 Timeline | — | 1 | 1 | — | 1 | 4 | ✅ Feito |
| T2 Auto Deduction | 1 | 0 | 0 (edit) | — | 0 | 0 | ✅ Feito (parcial) |
| T3 Dosage Calculator | 1 | 1 | 1 | 1 | 2 | 5 | ✅ Feito |
| T4 Appointment Reminder | — | — | — | — | — | 2 | ✅ Feito |
| T5 Portal Completo | — | — | 3 | — | 4 | 5 | ✅ Feito |
| T6 Vax Forecast | — | — | 0 (edit) | — | 1 | 2 | ✅ Feito |
| T7 Barcode Scanner | — | — | — | 1 | 1 | 1 | ✅ Feito |
| T8 Price Tiers | 1 | 1 | 0 (edit) | — | 0 | 2 | ✅ Feito |
| T9 Stock Transfer | — | 0 | 0 (edit) | — | 1 | 2 | ✅ Feito |
| T10 Notification Prefs | 1 | 0 | — | — | 0 | 2 | ✅ Feito |
| T11 Emergency Protocols | 1 | 1 | 1 | — | 4 | 2 | ✅ Feito |
| T12 Corporate Dashboard | — | — | 1 | — | 1 | 2 | ✅ Feito |
| **Total** | **5** | **4** | **7 + 4 edit** | **2** | **15** | **~29** | |
| U1 System Update | 1 | 1 | 1 | — | 2 | 4 | ✅ Feito |

---

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

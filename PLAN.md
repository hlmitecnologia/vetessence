# VetEssence — Build Plan (v4)

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
Tests:  242 Unit   +   272 Feature   =   514 total   (5 pre-existing failures)
```

| Suite | Count | Notes |
|-------|-------|-------|
| Unit/Models | 214 | All 45+ models covered |
| Unit/Services | 18 | EmailApi, Pix, BranchContext |
| Unit/Traits | 10 | Auditable, BranchScoped, HasPhoto |
| Feature/Controllers | 272 | 52 controllers, ~236 new, pre-existing portal/module failures |
| Feature/Commands | 15 | 8 commands |
| Feature/Integrations | 12 | 10 flow scenarios |
| **Total new** | **541** | Added in Phases H–M |
| Pre-existing failures | 5 | Portal `start_time`, ExampleTest, PrescriptionPrint, VaccinationProtocol |

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

## Remaining Pre-existing Failures (5)

| Test | Failure | Root Cause |
|------|---------|------------|
| `Portal/PetControllerTest::show` | 500 | Portal uses `start_time` column on `appointments` — DB has `time` |
| `Portal/PetControllerTest::index` | 500 | Same `start_time` issue |
| `Portal/AppointmentControllerTest::show` | 500 | Same `start_time` issue |
| `ExampleTest` | 404 | Tests `/` route which may not exist in test env |
| `PrescriptionPrintTest` | 500 | View or route issue |
| `VaccinationProtocolTest` | 403 | Gate check failing |

---

## Phase N — Regulatory Compliance (ANVISA + LGPD)

### N1. ANVISA (Portaria 344/98, RDC 44/2009) — Current State

| Requirement | Status | Action Needed |
|-------------|--------|---------------|
| Controlled substance schedule (A1–C1) | ✅ Done | — |
| Stock movement tracking | ✅ Done | — |
| Monthly/annual reports + CSV export | ✅ Done | — |
| Digital prescription storage | ✅ Done | — |
| **Substance → Prescription → Animal → Vet traceability** | ❌ Gap | Link `controlled_substance_logs` to prescriptions + pets |
| **Inventory reconciliation** | ❌ Gap | Add periodic physical count vs system with variance report |
| **Prescription retention (2 years)** | ❌ Gap | Add archival flag and retention policy |
| **ANVISA notification for discrepancies** | ❌ Gap | Add alerting for stock variances above threshold |

#### N1.1 Tasks
- [ ] Add `prescription_id` FK to `controlled_substance_logs`
- [ ] Add `pet_id` population in substance log creation (vet must specify which animal)
- [ ] Create inventory reconciliation table + reconciliation view
- [ ] Add variance alert system (email + notification)
- [ ] Implement 2-year retention with archival/deletion workflow
- [ ] Add ANVISA-controlled-substance watermark to prescription print

### N2. LGPD (Lei Geral de Proteção de Dados)

| Requirement | Status | Action Needed |
|-------------|--------|---------------|
| **Data processing consent (Art. 7, I)** | ❌ Missing | Create `consent_logs` table + UI for tutor collection |
| **Right to access (Art. 9)** | ❌ Missing | Add data export endpoint for tutor data |
| **Right to deletion (Art. 15)** | ❌ Missing | Add anonymization workflow (anonymize, don't hard-delete) |
| **Data retention policy** | ❌ Missing | Define retention periods per data category |
| **Security measures** | ⚠️ Partial | Has role-based access; needs encryption at rest audit |

#### N2.1 Tasks
- [ ] Create `consent_logs` migration: `id, tutor_id, consent_type (string: lgpd_comunicacao/lgpd_dados/lgpd_compartilhamento), granted (bool), ip_address, user_agent, created_at`
- [ ] Add consent registration UI on portal registration + first login
- [ ] Create data export command: exports all tutor/pet data as JSON
- [ ] Create anonymization command: replaces PII with `[ANONIMIZADO]` in tutors/pets, keeps operational records
- [ ] Update Privacy Policy page at `/privacidade`
- [ ] Add cookie consent banner on portal

### N3. CFMV (Conselho Federal de Medicina Veterinária)

| Requirement | Status | Action Needed |
|-------------|--------|---------------|
| Digital medical records (Res. 875/2006) | ✅ Done | Medical records exist |
| Prescription print (Res. 957/2006) | ✅ Partial | Basic print exists; needs CRMV number, digital signature |
| Telemedicine (Res. 1465/2022) | ⚠️ Partial | Teleconsultation room works; needs digital signature validation |
| Health certificate (Res. 974/2006) | ⚠️ Partial | PDF generation exists; needs certificate numbering validation |

#### N3.1 Tasks
- [ ] Add CRMV number + electronic signature hash to prescription print
- [ ] Implement digital signature (hash + timestamp) for medical records
- [ ] Add veterinarian's digital signature to health certificate PDFs

---

## Phase O — Test Gap Closure

### O1. Remaining Controller Coverage
- [ ] Fix Portal controllers (`start_time` → `time` column)
- [ ] Fix ExampleTest
- [ ] Fix PrescriptionPrintTest
- [ ] Fix VaccinationProtocolTest gate
- [ ] Add API controller tests (Api/*)
- [ ] Add Auth controller tests (Auth/*)

### O2. Missing Edge Cases
- [ ] Soft delete tests on models with `SoftDeletes`
- [ ] File upload tests (photo, exam files)
- [ ] Validation rule tests per controller
- [ ] Pagination tests on index endpoints
- [ ] Empty state tests (no records)

### O3. Performance Tests
- [ ] N+1 query detection on index views
- [ ] Pagination with large datasets

### O4. Pre-existing Schema–Model Mismatches
Known columns in model fillable that don't exist in DB (need migration to add or fillable to remove):

| Model | Missing Columns |
|-------|-----------------|
| `Appointment` | `duration`, `room`, `created_by` |
| `AppointmentService` | `discount` |
| `Category` | `description` |
| `Convenio` | `coverage`, `max_consults_month`, `contract_number`, `start_date`, `end_date` |
| `MedicalRecord` | `vet_id` (has `user_id`), `chief_complaint` (has `complaint`), `time`, `vital_signs`, `attachments`, `anamnesis`, `physical_exam`, `prognosis`, `record_id`, `version` |
| `Product` | `unit`, `barcode`, `max_stock` |
| `Surgery` | `anesthesia_type`, `surgery_duration`, `medical_record_id` |
| `Vaccination` | `vaccine_name` (has `vaccine`), `application_date` (has `date`), `lot_number` (has `batch`), `next_due_date` (has `next_date`), `dose` |

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

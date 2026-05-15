# VetEssence — Phase P: Feature Gap Closure Plan

## Overview
6 missing features identified from real veterinary practice workflow. Each includes: migration, model, relationships, validation, views (where applicable), gates/permissions, and tests.

---

## P1 — Euthanasia & Cremation Workflow

**Why**: `PetDeathRecord` model exists but lacks a complete clinical/administrative flow.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add `authorized_by`, `authorization_doc`, `cremation_type`, `cremation_pickup_date`, `cremation_notes`, `memorial_text` columns to `pet_death_records` | migration |
| 2 | Update `PetDeathRecord` model with fillable, casts, relationships | `app/Models/PetDeathRecord.php` |
| 3 | Create controller with index/create/store/edit/update | `app/Http/Controllers/PetDeathRecordController.php` |
| 4 | Auth gates (`pet-death-records.view`, `.create`, `.edit`) | `PermissionSeeder.php` |
| 5 | Full CRUD views (index, show, create/edit, print certificate) | `resources/views/pet-death-records/*` |
| 6 | Routes | `routes/web.php` |
| 7 | Sidebar link | AdminLTE config |
| 8 | **Tests**: Unit (fillable, relationships, scopes); Feature (CRUD, validation, gate access) | `tests/Unit/Models/PetDeathRecordTest` + `tests/Feature/Controllers/PetDeathRecordControllerTest` |

**Tests**: 15 (~15 assertions, covering CRUD + validation + gate)

---

## P2 — Pre-Anesthetic Evaluation

**Why**: No standardized risk-assessment form before surgery/anesthesia.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create `pre_anesthetic_evaluations` table (pet, surgery, ASA score, fasted, hydration, exam checklist, vet, status) | migration |
| 2 | Create `PreAnestheticEvaluation` model + factory | `app/Models/PreAnestheticEvaluation.php`, factory |
| 3 | Controller with CRUD | `app/Http/Controllers/PreAnestheticEvaluationController.php` |
| 4 | Gates (`pre-anesthetic.*`) | `PermissionSeeder.php` |
| 5 | Views (create/edit with ASA selector, exam checklist, show with print) | `resources/views/pre-anesthetic-evaluations/*` |
| 6 | Routes + sidebar | `routes/web.php`, config |
| 7 | **Tests**: Unit (3), Feature (CRUD + gate + validation, ~12) | test files |

**Tests**: 15

---

## P3 — Prescription Diet / Nutrition Management

**Why**: Vet practices prescribe therapeutic diets (renal, hepatic, urinary) — no tracking exists.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create `diet_plans` table (pet, medical_record_id, diet_type, brand, product_name, daily_amount, duration, instructions) | migration |
| 2 | Create `DietPlan` model + factory | `app/Models/DietPlan.php`, factory |
| 3 | Controller + views (create from medical record, index, print) | controller + `resources/views/diet-plans/*` |
| 4 | Gates | `PermissionSeeder.php` |
| 5 | **Tests**: Unit (3), Feature (~10) | test files |

**Tests**: 13

---

## P4 — Pet Insurance / Health Plan Claims

**Why**: Convênio/insurance verification + claim filing is manual in Brazilian clinics. Automation saves hours weekly.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add `pre_authorization_required`, `coverage_details`, `claim_form_url` to `convenios` | migration |
| 2 | Create `convenio_claims` table (convenio_pet_id, invoice_id, claim_number, status, amount_requested, amount_approved, filed_at, response_at) | migration |
| 3 | Create `ConvenioClaim` model + factory | model + factory |
| 4 | Add `claim()` relationship + `coverableAmount()` logic to `Invoice` | `app/Models/Invoice.php` |
| 5 | Controller + views (claims list, file claim, track status) | controller + views |
| 6 | Clone `convenios.edit` → add coverage checkboxes | existing view |
| 7 | **Tests**: Unit (4), Feature (~12) | test files |

**Tests**: 16

---

## P5 — CVI (International Travel Certificate)

**Why**: `HealthCertificate` model exists but CVI (Certificado Veterinário Internacional) is a distinct CFMV-required document with specific fields.

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Add to `health_certificates`: `cvi_number`, `destination_country`, `transport_mode`, `embarkation_date`, `crmv_emitter`, `valid_until`, `requirements_checklist` (JSON), `is_cvi` boolean | migration |
| 2 | Update `HealthCertificate` model (fillable, casts, scopes for CVI vs regular) | model |
| 3 | Add `generateCviNumber()` method + PDF print view with CFMV template | model, view |
| 4 | Extend controller to handle CVI-specific validation (rabies titer, microchip, etc.) | controller |
| 5 | **Tests**: Unit (3), Feature (~8) | test files |

**Tests**: 11

---

## P6 — ER Triage / Waiting Room Board

**Why**: Emergency clinics need a live triage board (queue, severity color, estimated wait).

**Tasks**:
| # | Task | Files |
|---|------|-------|
| 1 | Create `triage_records` table (pet, check-in time, severity [green/yellow/orange/red], chief_complaint, vitals, assigned_vet, status) | migration |
| 2 | Create `TriageRecord` model + factory | model + factory |
| 3 | Controller + Livewire component for real-time board | controller + Livewire |
| 4 | Gates | `PermissionSeeder.php` |
| 5 | View: color-coded board with drag-to-assign | `resources/views/triage/*` |
| 6 | **Tests**: Unit (3), Feature (~10) | test files |

**Tests**: 13

---

## Totals

| Feature | Migrations | Models | Controllers | Views | Tests |
|---------|-----------|--------|-------------|-------|-------|
| P1 Euthanasia | 1 | 1 edit | 1 | 4 | 15 |
| P2 Pre-anesthetic | 1 | 1 | 1 | 4 | 15 |
| P3 Diet Plans | 1 | 1 | 1 | 3 | 13 |
| P4 Insurance | 2 | 1 | 1 | 3 | 16 |
| P5 CVI | 1 | 1 edit | 1 edit | 1 edit | 11 |
| P6 Triage | 1 | 1 | 1 | 2 | 13 |
| **Total** | **7** | **5+2 edit** | **5+2 edit** | **16+2 edit** | **83** |

---

## Estimation
- **~8–10 hours** development + testing
- Follows existing patterns: migration → model → controller → views → routes → sidebar → gate
- All tests follow existing conventions: `ModuleTestCase` for feature, `DatabaseTransactions` for unit

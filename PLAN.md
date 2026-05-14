# VetEssence - Build Plan (v2)

## Context
Laravel 8, AdminLTE 3.2, Livewire 2, Spatie Permissions, MySQL, Tailwind CSS, Alpine.js.
Brazilian Portuguese. Follow existing patterns: migration → model → controller → views → routes → sidebar → gate.

## Completed (42+ modules)
All core clinic management features: dashboard KPIs, multi-branch, tutors, pets, appointments, medical records, vaccinations, vaccine protocols, vaccination reminders, exams, surgeries, prescriptions, treatment plans, digital consent, dental charting, weight tracking, zoonotic diseases, clinical report templates, drug interaction checker, health certificates (PDF), parasite control, hospitalization (daily records, fluid therapy, prescriptions), anesthesia monitoring, laboratory orders, imaging exams, lab equipment integration, products, stock, suppliers, categories, controlled substances, invoices (PIX), payment gateways, financial reports, communication templates, communication queue, notification logs, staff notes, boarding/grooming, teleconsultation, referrals, services, insurance plans (convenios), online booking, users, roles/permissions.

---

## Phase 1: Email Notification Service
**Replaces raw Mail::raw() with a configurable HTTP API integration.**

### 1.1 Config
- `config/email-api.php` reading from `.env`:
  - `EMAIL_API_URL` — POST endpoint
  - `EMAIL_API_TOKEN` — Bearer token for auth
- `.env.example` defaults added

### 1.2 Service
- `app/Services/EmailApiService.php`
  - `send(string $name, string $email, string $message): bool`
  - POSTs JSON `{name, email, message}` to `EMAIL_API_URL` with `Authorization: Bearer {token}`
  - Logs success/failure, returns boolean

### 1.3 Command: Process Communication Queue
- `app/Console/Commands/ProcessCommunicationQueue.php`
  - Signature: `queue:process`
  - Reads `communication_queue` where `sent_at IS NULL AND scheduled_at <= NOW()`
  - For `channel = email`: calls `EmailApiService::send()`, updates `sent_at` and `status`
  - For `channel = whatsapp`: logs warning (handled by WAHA service externally)
  - Scheduled via Kernel (`$schedule->command('queue:process')->everyMinute()`)

### 1.4 Refactor SendVaccineReminders
- `app/Console/Commands/SendVaccineReminders.php`
  - Replace `Mail::raw()` with `EmailApiService::send()`
  - Keep WhatsApp path as-is (WAHA service handles WhatsApp)

### 1.5 Files
| What | File |
|---|---|
| Config | `config/email-api.php` |
| Service | `app/Services/EmailApiService.php` |
| Command | `app/Console/Commands/ProcessCommunicationQueue.php` |
| Modify | `app/Console/Commands/SendVaccineReminders.php` |
| Modify | `app/Console/Kernel.php` — schedule `queue:process` |
| Modify | `.env.example` — add `EMAIL_API_URL`, `EMAIL_API_TOKEN` |

---

## Phase 2: FullCalendar Appointment View
**Replaces DataTables appointment list with an interactive calendar.**

### 2.1 Backend
- `app/Http/Controllers/Api/AppointmentController.php` — add `calendar()` method
  - Returns JSON of appointments in FullCalendar format: `{id, title, start, end, backgroundColor, extendedProps}`
  - Accepts `?start=DATE&end=DATE` range filters
- `routes/api.php` — `GET /api/appointments/calendar`

### 2.2 Frontend
- Install FullCalendar via npm: `@fullcalendar/core`, `@fullcalendar/daygrid`, `@fullcalendar/timegrid`, `@fullcalendar/interaction`
- Compile via Mix/Vite into `public/js/app.js`
- `resources/views/appointments/index.blade.php` — replace table with FullCalendar:
  - Month/week/day views
  - Click event → show appointment details modal
  - Click empty slot → create appointment modal (reuse Livewire AppointmentForm or inline modal)
  - Drag-and-drop to reschedule (update via API)
- Color-code by status (scheduled=blue, confirmed=green, in_progress=orange, completed=gray, cancelled=red)

### 2.3 Files
| What | File |
|---|---|
| API method | `app/Http/Controllers/Api/AppointmentController.php` (add `calendar`) |
| Route | `routes/api.php` |
| View | `resources/views/appointments/index.blade.php` (rewrite) |
| JS | `resources/js/app.js` or new `resources/js/appointments-calendar.js` |
| Modify | `webpack.mix.js` / `vite.config.js` |

---

## Phase 3: Client Web Portal
**Fully responsive portal at `/portal` for pet owners to access their pets' data.**

### 3.1 Auth Guard & Login
- `config/auth.php` — add `tutor` guard (session driver, `App\Models\Tutor` provider)
- `app/Models/Tutor.php` — implement `Authenticatable`: `password` field (nullable), `remember_token`
- Migration: add `password`, `remember_token`, `email_verified_at` to `tutors` table
- `app/Http/Controllers/Portal/Auth/LoginController.php`
- `app/Http/Controllers/Portal/Auth/RegisterController.php` — register by CPF/email, link to existing tutor or create
- `app/Http/Controllers/Portal/Auth/ForgotPasswordController.php`
- Routes under `/portal` prefix with `guest:tutor` and `auth:tutor` middleware groups

### 3.2 Portal Layout (Responsive)
- `resources/views/portal/layouts/master.blade.php`
  - Mobile-first, fully responsive (Tailwind CSS + Alpine.js)
  - Bottom navigation bar on mobile, sidebar on desktop
  - No AdminLTE dependency — lightweight custom design
  - Uses existing Tailwind CSS compilation pipeline
- Partial views: header, footer, navigation

### 3.3 Portal Dashboard
- `app/Http/Controllers/Portal/DashboardController.php`
- View: `resources/views/portal/dashboard.blade.php`
- Shows:
  - Tutor's name, quick stats (pets count, upcoming appointments)
  - Pet cards (photo, name, species, breed, age)
  - Next vaccination due
  - Next parasite control due
  - Upcoming appointments list

### 3.4 My Pets
- `app/Http/Controllers/Portal/PetController.php`
- Views: `resources/views/portal/pets/` — index, show
- Pet detail page with:
  - Profile (photo, name, species, breed, birth date, weight history chart)
  - Medical records (read-only list, expandable)
  - Vaccination history (date, vaccine, next date)
  - Parasite control history
  - Weight tracking chart (using Chart.js — already installed)

### 3.5 Book Appointment
- `app/Http/Controllers/Portal/AppointmentController.php`
- Form: select pet → select service type → select date/time → confirm
- Writes to `online_bookings` table (existing infrastructure)
- Views: `resources/views/portal/appointments/` — create, index

### 3.6 My Invoices & PIX Payment
- `app/Http/Controllers/Portal/InvoiceController.php`
- Views: `resources/views/portal/invoices/` — index, show
- List invoices for tutor's pets
- Show invoice detail with PIX QR code (existing `generatePix` method)
- "Mark as paid" button (manual confirmation, logs payment)

### 3.7 API Endpoints (for portal)
- `routes/api.php` — add public portal endpoints under `api/portal/`:
  - `GET api/portal/species` — list species for booking form
  - `GET api/portal/services` — list services
  - `GET api/portal/slots?date=&service_id=` — available time slots
- `app/Http/Controllers/Api/PortalController.php`

### 3.8 Routes Structure
```
portal/login
portal/register
portal/forgot-password
portal/reset-password

portal/dashboard          (auth:tutor)

portal/pets               (auth:tutor)
portal/pets/{pet}
portal/pets/{pet}/medical-records
portal/pets/{pet}/vaccinations
portal/pets/{pet}/weight-chart

portal/appointments        (auth:tutor)
portal/appointments/create
portal/appointments/{id}

portal/invoices            (auth:tutor)
portal/invoices/{id}
portal/invoices/{id}/pix

portal/logout              (auth:tutor)
```

### 3.9 Files
| What | File |
|---|---|
| Auth config | `config/auth.php` (add `tutor` guard, `tutors` provider) |
| Tutor model | `app/Models/Tutor.php` (add Authenticatable) |
| Migration | `database/migrations/2026_05_add_password_to_tutors.php` |
| Portal Controllers | `app/Http/Controllers/Portal/Auth/*.php`, `app/Http/Controllers/Portal/*.php` |
| Portal Views | `resources/views/portal/**/*.blade.php` (~15 files) |
| Portal Layout | `resources/views/portal/layouts/master.blade.php` |
| API Controller | `app/Http/Controllers/Api/PortalController.php` |
| Routes | `routes/web.php` (portal group), `routes/api.php` (portal endpoints) |

---

## Phase 4: Advanced Vet-Specific Features

### 4.1 Boarding Kennel Map
- `resources/views/boardings/kennel-map.blade.php`
  - Visual grid of kennels/cages, color-coded by occupancy
  - Drag-and-drop pet assignment to kennels
  - Data: `boarding_kennels` table (id, name, row, column, size, species)
  - Migration: `create_boarding_kennels_table`
  - Model: `app/Models/BoardingKennel.php`
  - Assign kennel_id to existing `boardings` table (migration add column)
  - Backend: `app/Http/Controllers/BoardingController.php` — add `kennelMap()`, `assignKennel()`

### 4.2 Euthanasia & Death Records
- Migration: add columns to `pets` or new `pet_death_records` table:
  - `death_date`, `cause_of_death`, `euthanasia`, `cremation`, `obituary_notes`
- Model: `app/Models/PetDeathRecord.php`
- CRUD: `app/Http/Controllers/PetDeathRecordController.php`
- Views: `resources/views/pet-death-records/` — index, create, show, edit
- Routes under `/pets/{pet}/death-record`
- Sidebar link under "Pets" section

### 4.3 Grooming Templates
- Migration: `create_grooming_templates_table` (name, species, breed, description, duration_minutes, price, included_services JSON)
- Migration: `create_grooming_appointments` (pet_id, tutor_id, template_id, date, status, notes)
- Model, controller, views for templates
- Integrate with existing boarding/appointment system

### 4.4 Therapy & Rehabilitation Tracking
- Migration: `create_therapy_sessions_table` (pet_id, condition, therapy_type, therapist_id, date, duration, notes, progress)
- Types: physiotherapy, hydrotherapy, acupuncture, laser, massage, other
- Model: `app/Models/TherapySession.php`
- CRUD: `app/Http/Controllers/TherapySessionController.php`
- Views: `resources/views/therapy-sessions/` (linked to pet profiles)
- Recurring session scheduling support

### 4.5 Breed-Specific Defaults
- Migration: `create_breed_defaults_table` (species, breed, avg_weight_min, avg_weight_max, lifespan_years, predispositions JSON, notes)
- Model: `app/Models/BreedDefault.php`
- CRUD: `app/Http/Controllers/BreedDefaultController.php`
- Views, routes, sidebar
- Display in pet profile if breed matches

### 4.6 Vaccination Certificate PDF
- `app/Http/Controllers/VaccinationController.php` — add `certificate($pet)` method
- View: `resources/views/vaccinations/certificate-pdf.blade.php`
- Uses `barryvdh/laravel-dompdf` (already installed)
- Lists all vaccinations for a pet in a formatted certificate layout
- Route: `vaccinations/{pet}/certificate`

### 4.7 Recurring Appointments
- Migration: add to `appointments` table: `is_recurring`, `recurrence_rule` (RRULE or JSON: frequency, interval, count, until)
- `app/Console/Commands/GenerateRecurringAppointments.php`
  - Scheduled daily: checks recurring appointments past their last occurrence, generates next
- Update `AppointmentController@store` to accept recurrence params
- Update appointment form with recurrence options (daily, weekly, biweekly, monthly)

### 4.8 Files Summary
| Feature | New/Modified Files |
|---|---|
| Kennel map | `create_boarding_kennels_table`, `BoardingKennel.php`, `BoardingController` (add methods), `kennel-map.blade.php` |
| Death records | `create_pet_death_records_table`, `PetDeathRecord.php`, `PetDeathRecordController.php`, views, routes |
| Grooming templates | `create_grooming_templates_table`, `GroomingTemplate.php`, controller, views |
| Therapy tracking | `create_therapy_sessions_table`, `TherapySession.php`, controller, views |
| Breed defaults | `create_breed_defaults_table`, `BreedDefault.php`, controller, views |
| Vacc cert PDF | `VaccinationController@certificate`, `certificate-pdf.blade.php` |
| Recurring appointments | migration add columns, `GenerateRecurringAppointments.php`, form updates |

---

## Phase 5: Daily Operations & Compliance
**Features essential for day-to-day clinic workflow, regulatory compliance, and client retention.**

### 5.1 Staff Scheduling / Shift Management
- Migration: `create_staff_schedules_table`
  - `user_id`, `date`, `start_time`, `end_time`, `branch_id`, `role` (vet, receptionist, assistant, groomer), `notes`
- Migration: `create_staff_time_off_table` (user_id, start_date, end_date, type, approved_by, status)
- Model: `app/Models/StaffSchedule.php`, `app/Models/StaffTimeOff.php`
- CRUD: `app/Http/Controllers/StaffScheduleController.php`
  - Calendar view (monthly) showing all staff on a given day
  - Drag to assign shifts
  - Conflict detection (same person scheduled twice)
- Views: `resources/views/staff-schedules/`
  - Monthly calendar grid
  - Daily schedule view with staff list and time blocks
  - Time-off request form and approval workflow
- Routes, sidebar under "Administração"
- Kernel command `staff:remind` — notifies staff of upcoming shifts via email

### 5.2 Waiting Room / Patient Flow Board
- `resources/views/appointments/flow-board.blade.php`
  - Real-time kanban-style board: **Check-in → Waiting → In Consultation → In Exam → In Surgery → Billing → Done**
  - Columns show appointment cards with pet name, tutor, time, vet
  - Drag-and-drop between columns to update status
  - Auto-refresh every 30s via Alpine.js polling
  - Display on wall-mounted screen in waiting room (fullscreen mode)
- Backend: `AppointmentController@flowBoard`, `AppointmentController@updateStatus` (partial update via AJAX)
  - `PATCH /appointments/{id}/status` — updates status, logs timestamp
  - `GET /appointments/flow-data` — returns appointments grouped by status for today
- Route: `GET appointments/flow-board`
- Link in sidebar under "Atendimento"

### 5.3 Client Communication History
- `app/Http/Controllers/TutorController.php` — add `communication($tutor)` method
- View: `resources/views/tutors/communication.blade.php`
  - Unified timeline showing:
    - Emails sent (from `notification_logs` + `communication_queue`)
    - WhatsApp messages sent (from `notification_logs`)
    - Appointments (from `appointments`)
    - Invoices generated (from `invoices`)
    - Staff notes about this tutor (from `staff_notes`)
  - Filterable by type, date range
  - Each entry shows: date, type, summary, status
- Route: `tutors/{tutor}/communication`

### 5.4 Prescription Print Template
- `app/Http/Controllers/PrescriptionController.php` — add `print($prescription)` method
- View: `resources/views/prescriptions/print.blade.php`
  - Professional layout with:
    - Clinic logo and header (from `branches` data)
    - Vet name, council number (CRMV)
    - Tutor and pet info
    - Medication list with dosage, frequency, duration, route
    - Date and signature line
  - Print-optimized CSS (no sidebar, no nav)
  - Uses `@media print` for clean paper output
- Route: `prescriptions/{prescription}/print`
- Button in prescription show view

### 5.5 Pet Auto-Age Calculator
- `app/Models/Pet.php` — add accessor:
  - `getAgeAttribute(): string` — computes from `birth_date`, returns formatted string like "3 anos e 2 meses"
  - `getAgeMonthsAttribute(): int` — total months for filtering
- Update all pet views (show, index, portal pet profile) to display age automatically
- Update `PetForm` Livewire component to show computed age when birth_date changes

### 5.6 Audit Log
- Migration: `create_audit_logs_table`
  - `user_id`, `action` (created, updated, deleted, viewed, exported), `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`
- Model: `app/Models/AuditLog.php`
- Trait: `app/Traits/Auditable.php`
  - On `created`/`updated`/`deleted` events, logs changes
  - Attach to key models: User, Tutor, Pet, Appointment, MedicalRecord, Prescription, Invoice, ControlledSubstance, Vaccination
- `app/Http/Controllers/AuditLogController.php`
  - Index with filters: model type, user, date range, action
  - Detail view showing old vs new values diff
- Views: `resources/views/audit-logs/`
- Route, sidebar under "Administração"
- Prunes logs older than 90 days via Kernel command

### 5.7 ANVISA Controlled Substance Reports
- `app/Http/Controllers/ControlledSubstanceController.php` — add methods:
  - `reportMonthly()` — generates monthly usage report by substance
  - `reportAnnual()` — annual inventory reconciliation
  - `exportCsv()` — CSV export for ANVISA submission
- Views: `resources/views/controlled-substances/reports/`
- Uses existing `controlled_substance_logs` data
- Route, sidebar link

### 5.8 Automated Recall Campaigns
- `app/Console/Commands/ProcessRecallCampaigns.php`
  - Signature: `recall:process`
  - Queries for overdue vaccinations (past `next_date`), overdue parasite controls
  - Groups by tutor, creates `communication_queue` entries with channel=email
  - Sends: "Oie! Notamos que a vacina {vaccine} do {pet} está atrasada. Agende já!"
- `app/Console/Commands/ProcessBirthdayCampaigns.php`
  - Signature: `birthday:process`
  - Daily: finds tutors with birthday today (from `tutors` or `users`)
  - Creates email greeting via `communication_queue`
- Schedule both in Kernel (`$schedule->command('recall:process')->dailyAt('08:00')`, `$schedule->command('birthday:process')->dailyAt('06:00')`)

### 5.9 Database Backup Management
- `app/Console/Commands/DatabaseBackup.php`
  - Signature: `db:backup`
  - Runs `mysqldump`, saves to `storage/app/backups/` with timestamp
  - Retention: keeps last 30 daily, 12 monthly
  - Optionally upload to S3-compatible storage
- `app/Console/Commands/DatabaseBackupCleanup.php`
  - Removes old backups beyond retention
- Schedule in Kernel
- Link in sidebar under "Administração" — backup list with download/restore buttons
- `app/Http/Controllers/BackupController.php`:
  - `index()` — lists available backups
  - `create()` — triggers backup
  - `download($file)` — serves backup file
  - `delete($file)` — removes backup

### 5.10 Files Summary
| Feature | New/Modified Files |
|---|---|
| Staff scheduling | `create_staff_schedules_table`, `StaffSchedule.php`, `StaffTimeOff.php`, `StaffScheduleController.php`, views, routes |
| Flow board | `flow-board.blade.php`, `AppointmentController` (add methods), routes |
| Comm history | `tutors/communication.blade.php`, `TutorController@communication`, route |
| Prescription print | `prescriptions/print.blade.php`, `PrescriptionController@print`, route |
| Age calculator | `Pet.php` (add accessors), update views |
| Audit log | `create_audit_logs_table`, `AuditLog.php`, `Auditable.php` trait, `AuditLogController.php`, views, routes |
| ANVISA reports | `ControlledSubstanceController` (add methods), views |
| Recall campaigns | `ProcessRecallCampaigns.php`, `ProcessBirthdayCampaigns.php`, Kernel schedule |
| Backup | `DatabaseBackup.php`, `DatabaseBackupCleanup.php`, `BackupController.php`, views, routes |

---

## Execution Order

```
Phase 1: Email Notification Service
   └─ Lightweight, unblocks communication
Phase 2: FullCalendar Appointment View
   └─ Improves daily scheduling UX
Phase 3: Client Web Portal
   └─ Largest phase — client-facing
Phase 4: Advanced Vet Features
   └─ Independent sub-items, can be parallelized
      ├─ 4.1 Kennel Map
      ├─ 4.2 Death Records
      ├─ 4.3 Grooming Templates
      ├─ 4.4 Therapy Tracking
      ├─ 4.5 Breed Defaults
      ├─ 4.6 Vacc Cert PDF
      └─ 4.7 Recurring Appointments
Phase 5: Daily Operations & Compliance
   └─ Essential for real-world clinic workflow
      ├─ 5.1 Staff Scheduling
      ├─ 5.2 Patient Flow Board
      ├─ 5.3 Comm History
      ├─ 5.4 Prescription Print
      ├─ 5.5 Age Calculator
      ├─ 5.6 Audit Log
      ├─ 5.7 ANVISA Reports
      ├─ 5.8 Recall Campaigns
      └─ 5.9 Backup Management
```

---

## Phase 6: Comprehensive Test Suite
**Full integration guarantee — every module, every flow, every boundary.**

### 6.1 Foundation
- `tests/TestCase.php` — verify base `ModuleTestCase` exists and works with MySQL (port 3307)
- `phpunit.xml` — ensure `<env name="DB_PORT" value="3307"/>` is set, test DB seeding runs before suite
- `.env.testing` — verify all connection variables, seed roles + permissions before tests

### 6.2 Existing Test Audit
- Run full suite: `php artisan test --testsuite=Feature --stop-on-failure`
- Fix any pre-existing failures (EmailVerificationTest, RegistrationTest, ExampleTest — unrelated Laravel boilerplate)
- Ensure 0 failing tests before adding new ones

### 6.3 Module Feature Tests (Covering All 50+ Modules)
Each module gets tests for:

| Test Area | What It Covers |
|---|---|
| **Index** | List renders, pagination works, empty state handled |
| **Create** | Form validation (required fields, unique constraints, foreign keys), successful creation, authorization gates |
| **Show** | Record found vs not found, data matches what was created |
| **Update** | Field changes persist, validation on edit, authorization |
| **Delete** | Record removed, cascade/restrict constraints respected, authorization |
| **Search/Filter** | Each filter parameter works correctly |
| **PDF/Export** | If module has PDF (health certs, invoices, prescription print): file is generated, content matches |
| **API** | If module has API endpoints: CRUD via JSON, auth via Sanctum tokens |
| **Edge Cases** | Null fields, max length, duplicate entries, special characters, large datasets |

**Modules to test (grouped by existing `tests/Feature/` structure or by domain):**

| Group | Modules | Existing Tests |
|---|---|---|
| Auth | Login, Register, Password Reset, Email Verification, Confirm Password | EmailVerificationTest, RegistrationTest |
| Admin | Users, Roles, Permissions, Branches | — |
| Cadastro | Tutors, Pets, Convenios, Pet-Tutor pivot | — |
| Atendimento | Appointments, Online Bookings | — |
| Clínico | Medical Records, Vaccinations, Vaccine Protocols, Vaccination Reminders, Exams, Surgeries, Prescriptions, Treatment Plans, Digital Consent, Consent Templates, Dental Charts, Weight Records, Zoonotic Diseases, Clinical Report Templates, Drug Interactions, Health Certificates, Parasite Control | ParasiteControlTest, HealthCertificateTest |
| Internação | Hospitalizations, Daily Records, Fluid Therapy, Prescriptions | — |
| Anestesia | Anesthesia Monitoring, Vital Signs | — |
| Laboratório | Lab Orders, Lab Tests, Imaging Exams, Lab Equipment Integrations | — |
| Farmácia | Products, Stock Movements, Suppliers, Categories, Controlled Substances, Controlled Substance Logs | — |
| Financeiro | Invoices, Invoice Items, Payment Gateways | DrugInteractionTest (flash message) |
| Comunicação | Communication Templates, Communication Queue, Notification Logs, Staff Notes | — |
| Hotel | Boardings, Boarding Daily Tasks, Grooming | — |
| Telemedicina | Teleconsultations | — |
| Encaminhamento | Referrals | — |
| Serviços | Services | — |

### 6.4 Service Tests
| Service | What to Test |
|---|---|
| `PixService` | Payload generation, CRC16 checksum validity, EMV format compliance, different values, QR code generation |
| `EmailApiService` | HTTP call made correctly, success/failure handling, auth header present |
| `DrugInteractionService` | Pair lookup, no-interaction case, multiple drugs, severity levels |
| `PaymentService` | (Stub) test that it returns expected placeholder |

### 6.5 Unit / Model Tests
| Model | What to Test |
|---|---|
| `User` | Role assignment, permission checks, scope filters |
| `Tutor` | Phone formatting, primary pet scope |
| `Pet` | Age accessor (years, months), species scopes, active scope |
| `Invoice` | Number generation, totals, PIX code generation |
| `Vaccination` | Reminder sent scope, overdue scope |
| `ParasiteControl` | Next due date scope |
| `HealthCertificate` | Number generation, sequence formatting |
| `AuditLog` | (Phase 6) Log creation on model events, pruning |
| `NotificationLog` | Channel scopes, date range scopes |
| `MedicalRecord` | SOAP field validation, prognosis enum |
| `Boarding` | Active scope, financial calculations |

### 6.6 Console Command Tests
| Command | What to Test |
|---|---|
| `vaccines:remind` | Sends correct emails, skips already-reminded, respects lookahead |
| `queue:process` | Processes pending queue items, updates status |
| `recall:process` | Detects overdue items, creates queue entries |
| `birthday:process` | Finds today's birthdays, creates greeting |
| `db:backup` | Creates file, respects retention, fails gracefully on permissions |
| `staff:remind` | Notifies about upcoming shifts |
| `GenerateRecurringAppointments` | Creates next occurrence correctly |

### 6.7 Integration Tests
| Scenario | What to Test |
|---|---|
| **Full appointment flow** | Create tutor → create pet → book appointment → create medical record → prescribe medication → generate invoice → mark paid |
| **Vaccination cycle** | Create vaccination → reminder sent → notification logged → `reminder_sent` flag set |
| **Hospitalization cycle** | Admit patient → add daily record → add fluid therapy → prescribe medication → discharge |
| **Client portal** | Register tutor → login → view pets → book appointment → view invoice |
| **FullCalendar** | Events endpoint returns correct JSON format, drag-resize updates correctly |
| **Waiting room flow** | Check-in → wait → consult → done, status transitions valid |
| **Audit trail** | Creating a medical record generates audit log entry |
| **Boarding flow** | Check-in → assign kennel → add daily task → checkout → calculate total |
| **Controlled substance** | Add substance → record movement → verify balance → generate ANVISA report |

### 6.8 Test Data Factories
- Verify all factories exist and produce valid models:
  - `UserFactory`, `TutorFactory`, `PetFactory`, `AppointmentFactory`, `MedicalRecordFactory`, `VaccinationFactory`, `ExamFactory`, `SurgeryFactory`, `PrescriptionFactory`, `InvoiceFactory`, `ProductFactory`, `ServiceFactory`, `CategoryFactory`, `SupplierFactory`, `BranchFactory`, `ConvenioFactory`, `VaccineProtocolFactory`, `ParasiteControlFactory`, `HealthCertificateFactory`, `DrugInteractionFactory`, `ClinicalReportTemplateFactory`, `HospitalizationFactory`, `DailyRecordFactory`, `FluidTherapyFactory`, `HospitalizationPrescriptionFactory`, `AnesthesiaMonitoringFactory`, `VitalSignFactory`, `LabOrderFactory`, `LabTestFactory`, `ImagingExamFactory`, `BoardingFactory`, `BoardingDailyTaskFactory`, `TeleconsultationFactory`, `ReferralFactory`, `TreatmentPlanFactory`, `TreatmentPlanItemFactory`, `ConsentTemplateFactory`, `ConsentFormFactory`, `DentalChartFactory`, `DentalConditionFactory`, `WeightRecordFactory`, `ZoonoticDiseaseFactory`, `StaffNoteFactory`, `CommunicationTemplateFactory`, `CommunicationQueueFactory`, `NotificationLogFactory`, `LabEquipmentIntegrationFactory`, `PaymentGatewayFactory`
  - Add any missing factories

### 6.9 Seeders for Testing
- Update `database/seeders/DatabaseSeeder.php` to work in test environment
- Create `database/seeders/TestDatabaseSeeder.php`:
  - Roles and permissions
  - Sample branches
  - Sample users (admin, vet, receptionist)
  - Sample tutors with pets
  - Sample appointments, medical records, vaccinations
  - Sample invoices, products, stock movements
- Ensure seed is idempotent (can run multiple times)

### 6.10 CI / Pre-Commit Hook
- `.github/workflows/tests.yml` — GitHub Actions workflow:
  - Start MySQL service
  - Run migrations
  - Execute full test suite
  - Report results
- Or `pre-commit` git hook:
  - Runs `php artisan test --testsuite=Feature --stop-on-failure`
  - Blocks commit if any test fails

### 6.11 Coverage Target
- **Goal:** >90% pass rate across all feature tests
- **Goal:** Every controller action covered by at least one test
- **Goal:** Every service method covered
- **Goal:** Edge cases documented and tested (nulls, duplicates, permissions, not-found)

### 6.12 Files Summary
| What | Details |
|---|---|
| ~200+ test files | One per module action (index, create, store, show, edit, update, destroy + search + PDF + API) |
| Factory audit | Verify all factories exist, fix missing |
| `TestDatabaseSeeder` | Idempotent seed for test environment |
| `.github/workflows/tests.yml` | CI pipeline |
| `pre-commit` hook | Optional git hook |
| `phpunit.xml` | Verify MySQL test DB config |
| `ModuleTestCase` | Verify base class works consistently |

---

## Final Execution Order

```
Phase 1: Email Notification Service        (small)
Phase 2: FullCalendar Appointment View     (medium)
Phase 3: Client Web Portal                 (large)
Phase 4: Advanced Vet Features             (medium, parallelizable)
Phase 5: Daily Operations & Compliance     (medium, parallelizable)
Phase 6: Comprehensive Test Suite          (large, ongoing)
```

## Rules
1. Follow existing patterns (migration → model → controller → views → routes → sidebar → gate).
2. Verify: `php artisan route:list 2>&1 | grep -c 'Target class'` (must be 0)
3. Syntax check: `php -l` on all new PHP files.
4. Cache: `php artisan route:clear && composer dump-autoload` after changes.
5. Run tests: `php artisan test --testsuite=Feature --stop-on-failure` after each phase.
6. All new views responsive (Bootstrap 4 / Tailwind).
7. Portuguese labels for UI, English for code identifiers.
8. `/portal` routes use `tutor` auth guard; existing routes use `web` guard.

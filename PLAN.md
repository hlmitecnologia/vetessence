# VetEssence — Build Plan (v3)

## Context
Laravel 8, AdminLTE 3.2, Livewire 2, Spatie Permissions, MySQL, Tailwind CSS, Alpine.js.
Brazilian Portuguese. Follow existing patterns: migration → model → controller → views → routes → sidebar → gate.

## Completed (50+ modules)
All core clinic management features: dashboard KPIs, multi-branch, tutors, pets, appointments, medical records, vaccinations, vaccine protocols, vaccination reminders, exams, surgeries, prescriptions, treatment plans, digital consent, dental charting, weight tracking, zoonotic diseases, clinical report templates, drug interaction checker, health certificates (PDF), parasite control, hospitalization (daily records, fluid therapy, prescriptions), anesthesia monitoring, laboratory orders, imaging exams, lab equipment integration, products, stock, suppliers, categories, controlled substances, invoices (PIX), payment gateways, financial reports, communication templates, communication queue, notification logs, staff notes, boarding/grooming, teleconsultation, referrals, services, insurance plans (convenios), online booking, users, roles/permissions, email notification service, FullCalendar appointments, client web portal, breed defaults, vaccination certificate PDF, recurring appointments, pet auto-age, prescription print, staff scheduling, patient flow board, communication history, audit log, ANVISA reports, recall campaigns, backup management, comprehensive test suite.

---

## Design Pillars

1. **Tutors & pets are global** — shared across branches. `created_at_branch_id` logs registration origin. Attendance tracked via operational records (branch-scoped). No restriction — any branch serves any tutor/pet.
2. **Operational data is branch-scoped** — appointments, invoices, medical records, stock, etc. belong to one branch.
3. **Users belong to a home branch** — `branch_id` on `users`. `NULL` = global access (super-admin, super-financial, HR, auditor).
4. **Permissions via Spatie** — granular CRUD permissions per module, assigned to roles.

---

## Phase A — Schema & Migrations

### A1. New Tables
- `departments` — id, name, description, timestamps
- `positions` — id, name, description, department_id (FK), timestamps

### A2. Columns to Add

**Operational tables — add `branch_id` (nullable FK → `branches`):**
`appointments`, `invoices`, `invoice_items`, `medical_records`, `vaccinations`, `exams`, `surgeries`, `prescriptions`, `treatment_plans`, `consent_forms`, `dental_charts`, `weight_records`, `hospitalizations`, `hospitalization_daily_records`, `hospitalization_fluid_therapies`, `hospitalization_prescriptions`, `anesthesia_monitorings`, `laboratory_orders`, `lab_tests`, `imaging_exams`, `controlled_substances`, `controlled_substance_logs`, `stock_movements`, `suppliers`, `categories`, `boardings`, `boarding_daily_tasks`, `therapy_sessions`, `teleconsultations`, `referrals`, `communication_queue`, `notification_logs`, `staff_notes`, `staff_schedules`, `staff_time_off`, `audit_logs`, `online_bookings`, `payment_gateways`, `lab_equipment_integrations`

**Tutors — add `created_at_branch_id`** (nullable FK → `branches`, logging only)
**Pets — add `created_at_branch_id`** (nullable FK → `branches`, logging only)

**Users — HR fields:**
`department_id` (FK → `departments`), `position_id` (FK → `positions`), `hire_date` (date), `contract_type` (string: clt, pj, estagio, terceiro, voluntario), `crmv` (string), `emergency_contact` (string), `emergency_phone` (string), wire up `branch_id` (already in table)

**Staff schedules — on-call:**
Remove unique `(user_id, work_date)`, add `is_on_call` (bool), `on_call_type` (string), `branch_id` (FK)

### A3. Models
- `Department` — fillable: name, description; hasMany Positions
- `Position` — fillable: name, description, department_id; belongsTo Department
- Update `User` — add belongsTo Department, Position, Branch; HR fillable fields
- Update `Tutor` — add belongsTo Branch (created_at_branch)
- Update `Pet` — add belongsTo Branch (created_at_branch)
- All operational models — add belongsTo Branch, BranchScope global scope

### A4. Tests
- Migration tests: columns exist, FKs work, defaults correct
- Factory tests: DepartmentFactory, PositionFactory produce valid models
- Model tests: relationships, fillable, casts

---

## Phase B — Roles & Permissions

### B1. All Roles

| Role | Slug | Branch Scope | Access |
|---|---|---|---|
| Super Admin | super-admin | All (global) | Full system; manage users, roles, branches |
| Branch Admin | branch-admin | Own branch | Full operational access in branch; manage staff |
| Veterinarian | veterinarian | Own branch | Medical operations, appointments, records |
| Receptionist | receptionist | Own branch | Scheduling, tutor/pet registration, check-in |
| Financial | financial | Own branch | Invoices, payments, financial reports |
| Super Financial | super-financial | All (global) | Financial data across ALL branches |
| Stock Manager | stock-manager | Own branch | Products, stock, suppliers |
| Human Resources | human-resources | All (global) | Employee records, departments, positions |
| Tutor | tutor | Portal only | Own pets, appointments, invoices |
| Auditor | auditor | All (read-only) | View any data, no mutations |

### B2. Permissions (80+ seeded via Spatie)
Each module: `{module}.view`, `.create`, `.edit`, `.delete`

Permission groups: admin, users, roles, branches, departments, positions, employees, tutors, pets, appointments, medical-records, vaccinations, exams, surgeries, prescriptions, treatment-plans, consent-forms, dental-charts, weight-records, hospitalizations, anesthesia, laboratory, imaging, controlled-substances, products, stock, suppliers, categories, invoices, payments, financial-reports, boardings, therapy-sessions, teleconsultations, referrals, services, convenios, communication, notification-logs, staff-notes, staff-schedules, online-bookings, audit-logs, backups, drug-interactions, clinical-report-templates, health-certificates, vaccine-protocols, vaccination-reminders, parasite-controls, lab-equipment, payment-gateways, zoonotic-diseases, grooming-templates, breed-defaults, pet-death-records, schedules-on-call

### B3. Tests
- PermissionSeeder test: all permissions exist, correct count
- RoleSeeder test: all roles exist, correct permissions assigned
- Authorization test: each role can/cannot access appropriate endpoints

---

## Phase C — Middleware & Scoping

### C1. SetBranchContext Middleware
- Reads `auth()->user()->branch_id`
- Sets `context('branch_id')` for the request
- Global users (`branch_id = null`) see all
- Registered in kernel, applied to web routes

### C2. BranchScope (Global Scope)
- All operational models get BranchScope
- Filters by `context('branch_id')` when set
- Skipped for global users (branch_id = null)

### C3. Auto-set branch_id on Create
- Controllers auto-fill `branch_id` from context on creation
- Global users can specify target branch optionally

### C4. Tests
- Middleware test: branch context set correctly, global users bypass
- Scope test: branch users only see own branch data, global users see all
- Controller test: new records get correct branch_id

---

## Phase D — HR Features

### D1. Departments CRUD
- DepartmentController (index, create, store, edit, update, destroy)
- Views: index, create, edit, show
- Routes: `admin/departments`
- Sidebar: Administração → Departamentos
- Gate: `departments.*`

### D2. Positions CRUD
- PositionController (index, create, store, edit, update, destroy)
- Views: index, create, edit, show (with department dropdown)
- Routes: `admin/positions`
- Sidebar: Administração → Cargos
- Gate: `positions.*`

### D3. Employee Management
- Extend UserController create/edit views with HR fields
- Employee listing at `admin/employees` with filters (department, position, branch, contract type, active/inactive)
- Routes: `admin/employees`
- Sidebar: Administração → Funcionários
- Gate: `employees.*`

### D4. Contract Types
- Config-driven: `config/hr.php` with contract_types array

### D5. Tests
- CRUD tests for departments, positions
- Employee creation with all HR fields
- Filter tests for employee listing
- Gate authorization tests

---

## Phase E — On-Call Scheduling

### E1. StaffSchedule Enhancements
- Remove unique constraint `(user_id, work_date)`
- Add `is_on_call`, `on_call_type`, `branch_id`
- Calendar view at `staff-schedules/on-call-calendar`
- Color-coded shifts (regular=blue, on-call=red)
- Conflict detection (overlapping shifts)

### E2. On-Call Reminder Command
- `staff:remind` command — notifies staff of next-day shifts
- Scheduled at 18:00 daily

### E3. Tests
- Multiple shifts per day test
- On-call calendar rendering test
- Conflict detection test
- Reminder command test

---

## Phase F — Super Financial Role

- New role `super-financial`, global scope (branch_id = null)
- Financial permissions across all branches
- Branch filter on financial UI
- No access to non-financial modules

### Tests
- Super Financial can view invoices from any branch
- Super Financial cannot access medical records

---

## Phase G — Gate & Controller Authorization Sweep

### G1. Rewrite Gates
- All 38+ gates rewritten to check Spatie permissions
- `Gate::before` retains super-admin bypass
- Branch-context-aware checks where needed

### G2. Controller Updates
- All `store`/`create` methods set `branch_id` from context
- All controllers get `$this->authorize()` or middleware gates
- ~50 controllers audited and updated

### G3. Tests
- Every gate tested: authorized and unauthorized scenarios
- Every controller action tested with correct role
- Branch isolation verified (user from branch A cannot modify branch B data)

---

## Execution Order

```
Phase A: Schema & Migrations        (foundation)
Phase B: Roles & Permissions         (Spirit seeders)
Phase C: Middleware & Scoping         (branch context)
Phase D: HR Features                 (departments, positions, employees)
Phase E: On-Call Scheduling          (calendar, rotation)
Phase F: Super Financial Role        (cross-branch finance)
Phase G: Gates & Controller Sweep    (authorization everywhere)
```

Each phase includes its own test suite. Run `php artisan test` after each phase.

## Rules
1. Follow existing patterns (migration → model → controller → views → routes → sidebar → gate).
2. Verify: `php artisan route:list 2>&1 | grep -c 'Target class'` (must be 0)
3. Syntax check: `php -l` on all new PHP files.
4. Cache: `php artisan route:clear && composer dump-autoload` after changes.
5. Run tests: `php artisan test --stop-on-failure` after each phase.
6. All new views responsive (Bootstrap 4 / Tailwind).
7. Portuguese labels for UI, English for code identifiers.
8. `/portal` routes use `tutor` auth guard; existing routes use `web` guard.

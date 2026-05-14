# VetEssence - Implementation Plan

## Context
Laravel 8, AdminLTE 3, Livewire 2, Spatie permissions, MySQL.
Brazilian Portuguese labels. Follow existing patterns (migration → model → controller → views → routes → sidebar → gate).

## Complete Modules (34)
All core modules done: Zoonosis, Hospitalization, Weight Tracking, Anesthesia, Treatment Plans, Digital Consent, Dental Charting, Controlled Substances, Communication (templates + queue), Laboratory, Imaging, Referrals, Appointments, Medical Records, Vaccinations, Exams, Surgeries, Prescriptions, Invoices, Products, Stock, Suppliers, Services, Categories, Users, Roles, Convenios, Vaccination Reminders, Notification Logs.

## Build Queue

### Phase 1: Vaccination Protocol Engine
Auto-suggest vaccine schedules by species/age. New table, CRUD, integration with vaccination form.

### Phase 2: Parasite Control Tracking
Flea/tick/heartworm prevention schedules. New module like "Vaccinations" for parasite products.

### Phase 3: Dashboard KPIs
Daily revenue, procedures count, no-show rate, hospitalization occupancy, upcoming reminders.

### Phase 4: Health Certificates
Formatted PDF export docs for travel/boarding. Requires dompdf package.

### Phase 5: Clinical Report Templates
Pre-built SOAP templates by species/specialty, like consent templates but for medical records.

### Phase 6: Drug Interaction Checker
Drug interaction DB + service to flag conflicts during prescribing.

### Phase 7: Boarding/Grooming
Check-in/check-out flow with daily task tracking for boarding pets.

### Phase 8: Internal Notes / Staff Comms
Quick note-taking and messaging between vets/reception.

### Phase 9: Online Booking
Customer-facing scheduling frontend using existing API endpoints.

### Phase 10: Lab Equipment Integration
Import endpoints for analyzers (placeholder/adaptable).

### Phase 11: Payment Gateway
Real card/PIX integration (currently stub).

### Phase 12: Multi-branch
Separate units under one installation.

### Phase 13: Teleconsultation
Video call within the system.

## Rules
1. After compaction: read this file, continue from current phase.
2. Verify: `php artisan route:list 2>&1 | grep -c 'Target class'` (must be 0)
3. Syntax check: `php -l` on all new PHP files.
4. Cache: `php artisan route:clear && composer dump-autoload` after changes.
5. Mark `[x]` when done.

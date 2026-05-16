# VetEssence — Veterinary Practice Management System

**VetEssence** is a comprehensive SaaS-style management system built for veterinary clinics. It handles clinical records, pharmacy, financials, regulatory compliance (ANVISA, LGPD, CFMV), and daily operational workflows — all accessible via web, mobile, and API.

Built with **Laravel 8**, **AdminLTE 3.2**, **Livewire 2**, **Spatie Permissions**, **MySQL**, **Tailwind CSS**, and **Alpine.js**. Brazilian Portuguese UI, English codebase.

---

## Features

### Clinical
- **Medical Records** — SOAP-based records with diagnosis, anamnesis, treatment plans
- **Prescriptions** — Digital Rx with medication, dosage, duration; QR-code verification
- **Vaccinations** — Protocol-based schedules, multi-dose tracking, certificate PDF (CFMV layout)
- **Surgeries** — Scheduling, anesthesia monitoring, pre-anesthetic evaluation
- **Hospitalizations** — Daily records, clinical evolution, discharge summary
- **Laboratory** — Test orders, sample tracking, result entry
- **Imaging** — X-ray, ultrasound, CT exam orders with reports
- **Dental Charts** — Odontological records and procedure tracking
- **Teleconsultations** — Remote consultation scheduling and notes
- **Weight Records** — Growth/weight tracking over time
- **Parasite Control** — Deworming and ectoparasite treatment schedules
- **Drug Interactions** — Cross-check tool for medication conflicts

### Triage & Emergency
- **Triage Board** — Livewire real-time board with severity colors (red/orange/yellow/green), 5s polling, audio alerts
- **Emergency Protocols** — Pre-configured urgent-care templates

### Agenda & Scheduling
- **Visual Calendar** — FullCalendar 6 with day/week/month views, drag-and-drop, color-coded by vet/procedure
- **Appointments** — CRUD with online booking integration
- **Staff Schedules** — Vet/receptionist shifts and on-call scheduling

### Pharmacy & Inventory
- **Products** — Full catalog with SKU, barcode, cost/sale price, batch/lot tracking
- **Stock Management** — Movements (in/out/adjustment/transfer), low-stock alerts
- **Purchase Orders** — Procurement workflow: draft → order → receive → reconcile
- **Controlled Substances** — ANVISA-compliant tracking with usage logs
- **Suppliers** — Vendor management and order history

### Financial
- **Invoices** — Service/product billing with payment tracking
- **Payments** — Multi-method (cash, card, PIX via gateway), payment plans
- **Bank Reconciliation** — Statement import and automated matching
- **Commissions** — Vet/employee commission calculation per procedure or product
- **Insurance Claims** — Convênio claim filing, auto-claim command, webhook receiver
- **Financial Reports** — Revenue, receivables, payment method breakdowns

### Regulatory & Compliance
- **ANVISA** — Controlled substance logs, prescription requirements, batch tracking
- **LGPD** — Consent forms, data retention policies, privacy audit
- **CFMV** — Res. 974/2006 compliant health certificates (CVI), vaccination certificates, digital signature block
- **Prescription Verification** — SHA-256 hash verification via public QR code URL (`/r/{hash}`), rate-limited
- **Audit Trail** — All changes logged with user, timestamp, IP

### Pet Management
- **Tutor & Pet Registration** — Global registry shared across branches
- **Health Certificates** — CVI templates with CRMV seal, transport/destination fields
- **Vaccination Reminders** — Automated email/SMS reminders for due doses
- **Pet Death Records** — Death registration with cause, necropsy flag
- **Microchip Tracking** — ID chip registration and lookup
- **Boarding / Grooming** — Hotel stays, bath & grooming with check-in/check-out workflow

### Communication
- **WhatsApp/SMS Provider** — Z-API integration for WhatsApp, SMS fallback, configurable channel per template
- **Internal Chat** — Livewire real-time chat between staff with unread badges
- **Staff Notes** — Internal notes for clinic team
- **Communication Queue** — Batch message processing via Artisan command
- **Notification Logs** — Delivery status tracking for all outbound messages

### Mobile
- **Mobile-Responsive Layout** — Bottom navigation bar (Início, Triagem, Receitas, Prontuários), simplified views for field vets

### Administration
- **User Management** — Role-based access (10 roles), CRUD permissions
- **Branch Management** — Multi-unit support with branch-scoped data
- **Categories** — Service/product categorization
- **Consent Templates** — Reusable legal consent forms
- **Communication Templates** — Pre-defined message templates per channel
- **Backup** — Automated database backup with retention

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 8, PHP 8.x |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Components | Livewire 2, FullCalendar 6, Chart.js |
| Database | MySQL |
| Auth | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |
| Testing | PHPUnit, DatabaseTransactions |

---

## Test Suite

```
293 Unit + 385 Feature = 678 tests (0 failures)
```

---

## Quick Start

```bash
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## About this file

This README is generated from [PLAN.md](./PLAN.md). On each git push, run:

```bash
cp PLAN.md README.md
```

to keep the build plan and test status in sync with the README.

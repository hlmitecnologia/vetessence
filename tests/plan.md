# VetEssence — Test Plan (Persistence-Based)

## Strategy

All tests use **real MySQL persistence** via `DatabaseTransactions` (rollback after each test, no schema rebuild needed). A dedicated `vetessence_testing` database on the same MySQL server (`192.168.0.150`).

Runner: `phpunit` (Laravel 8).

## Database Setup (one-time)

```sql
CREATE DATABASE IF NOT EXISTS vetessence_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run migrations:
```bash
php artisan migrate --env=testing
php artisan db:seed --env=testing --class=RoleSeeder
```

## Test Organization

```
tests/
  Feature/
    Modules/
      VaccinationProtocolTest.php
      ParasiteControlTest.php
      HealthCertificateTest.php
      ClinicalReportTemplateTest.php
      DrugInteractionTest.php
      BoardingTest.php
      StaffNoteTest.php
      OnlineBookingTest.php
      LabEquipmentTest.php
      PaymentGatewayTest.php
      BranchTest.php
      TeleconsultationTest.php
    Services/
      DrugInteractionServiceTest.php
      PaymentServiceTest.php
  Unit/
    Models/
      VaccineProtocolTest.php
      HealthCertificateTest.php
      DrugInteractionTest.php
      ...
```

## What Each Test Covers

| Module | Persistence | Auth | CRUD | Edges |
|--------|-----------|------|------|-------|
| VaccineProtocol | Create + suggestForPet() | Gate `protocolo-vacinas` | Store/update/destroy | Duplicate slug, species filter |
| ParasiteControl | Full CRUD | Gate `parasitario` | All routes | Pet scoping, date validation |
| HealthCertificate | Create + PDF | Gate `certificado-sanitario` | Store/pdf() | generateNumber() uniqueness |
| ClinicalReportTemplate | CRUD + slug | Gate `modelo-laudo` | All routes | Auto-slug, species scope |
| DrugInteraction | CRUD + service check | Gate `interacao-medicamentosa` | Store + checkApi | Duplicate detection (A↔B) |
| Boarding | Check-in/out + tasks | Gate `hospedagem` | Store/checkout/cancel | Status transitions |
| StaffNote | Create + markRead | Gate `nota-interna` | Inbox/sent | Own-note-only edit guard |
| OnlineBooking | Submit + confirm | Gate `agendamento-online` | confirm/reject | Pet creation on confirm |
| LabEquipment | Integration + receive | Gate `integracao-equipamentos` | API receive | Inactive integration reject |
| PaymentGateway | CRUD + active singleton | Gate `gateway-pagamento` | Toggle active | Only-one-active constraint |
| Branch | CRUD + user association | Gate `unidades` | Destroy guard | Users attached guard |
| Teleconsultation | Schedule + start/end | Gate `teleconsulta` | Start/end/room | Token generation, status flow |

## Running Tests

```bash
# Full suite (creates and migrates testing DB)
php artisan migrate --env=testing --force
phpunit

# Single module
phpunit tests/Feature/Modules/VaccinationProtocolTest.php
```

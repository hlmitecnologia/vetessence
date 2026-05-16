<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // ===== DEFINE ALL PERMISSIONS =====
        $permissions = [
            // Admin / System
            'admin.view', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'branches.view', 'branches.create', 'branches.edit', 'branches.delete',

            // HR
            'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            'positions.view', 'positions.create', 'positions.edit', 'positions.delete',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',

            // Cadastro
            'tutors.view', 'tutors.create', 'tutors.edit', 'tutors.delete',
            'pets.view', 'pets.create', 'pets.edit', 'pets.delete',
            'convenios.view', 'convenios.create', 'convenios.edit', 'convenios.delete',

            // Atendimento
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
            'online-bookings.view', 'online-bookings.create', 'online-bookings.edit', 'online-bookings.delete',

            // Clinico
            'medical-records.view', 'medical-records.create', 'medical-records.edit', 'medical-records.delete',
            'vaccinations.view', 'vaccinations.create', 'vaccinations.edit', 'vaccinations.delete',
            'exams.view', 'exams.create', 'exams.edit', 'exams.delete',
            'surgeries.view', 'surgeries.create', 'surgeries.edit', 'surgeries.delete',
            'prescriptions.view', 'prescriptions.create', 'prescriptions.edit', 'prescriptions.delete',
            'treatment-plans.view', 'treatment-plans.create', 'treatment-plans.edit', 'treatment-plans.delete',
            'consent-forms.view', 'consent-forms.create', 'consent-forms.edit', 'consent-forms.delete',
            'dental-charts.view', 'dental-charts.create', 'dental-charts.edit', 'dental-charts.delete',
            'weight-records.view', 'weight-records.create', 'weight-records.edit', 'weight-records.delete',
            'vaccine-protocols.view', 'vaccine-protocols.create', 'vaccine-protocols.edit', 'vaccine-protocols.delete',
            'vaccination-reminders.view', 'vaccination-reminders.create', 'vaccination-reminders.edit', 'vaccination-reminders.delete',
            'parasite-controls.view', 'parasite-controls.create', 'parasite-controls.edit', 'parasite-controls.delete',
            'health-certificates.view', 'health-certificates.create', 'health-certificates.edit', 'health-certificates.delete',
            'drug-interactions.view', 'drug-interactions.create', 'drug-interactions.edit', 'drug-interactions.delete',
            'clinical-report-templates.view', 'clinical-report-templates.create', 'clinical-report-templates.edit', 'clinical-report-templates.delete',
            'zoonotic-diseases.view', 'zoonotic-diseases.create', 'zoonotic-diseases.edit', 'zoonotic-diseases.delete',

            // Internacao
            'hospitalizations.view', 'hospitalizations.create', 'hospitalizations.edit', 'hospitalizations.delete',

            // Anestesia
            'anesthesia.view', 'anesthesia.create', 'anesthesia.edit', 'anesthesia.delete',

            // Laboratorio
            'laboratory.view', 'laboratory.create', 'laboratory.edit', 'laboratory.delete',
            'imaging.view', 'imaging.create', 'imaging.edit', 'imaging.delete',

            // Farmacia
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'stock.view', 'stock.create', 'stock.edit', 'stock.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'controlled-substances.view', 'controlled-substances.create', 'controlled-substances.edit', 'controlled-substances.delete',

            // Financeiro
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            'financial-reports.view', 'financial-reports.create', 'financial-reports.delete',

            // Hotel / Servicos
            'boardings.view', 'boardings.create', 'boardings.edit', 'boardings.delete',
            'therapy-sessions.view', 'therapy-sessions.create', 'therapy-sessions.edit', 'therapy-sessions.delete',
            'services.view', 'services.create', 'services.edit', 'services.delete',
            'grooming-templates.view', 'grooming-templates.create', 'grooming-templates.edit', 'grooming-templates.delete',
            'breed-defaults.view', 'breed-defaults.create', 'breed-defaults.edit', 'breed-defaults.delete',
            'pet-death-records.view', 'pet-death-records.create', 'pet-death-records.edit', 'pet-death-records.delete',

            // Comunicacao
            'communication.view', 'communication.create', 'communication.edit', 'communication.delete',
            'notification-logs.view', 'notification-logs.delete',
            'staff-notes.view', 'staff-notes.create', 'staff-notes.edit', 'staff-notes.delete',

            // Telemedicina / Encaminhamento
            'teleconsultations.view', 'teleconsultations.create', 'teleconsultations.edit', 'teleconsultations.delete',
            'referrals.view', 'referrals.create', 'referrals.edit', 'referrals.delete',

            // Pre-Anesthetic Evaluation
            'pre-anesthetic.view', 'pre-anesthetic.create', 'pre-anesthetic.edit', 'pre-anesthetic.delete',

            // Diet Plans
            'diet-plans.view', 'diet-plans.create', 'diet-plans.edit', 'diet-plans.delete',

            // Convenio Claims
            'convenio-claims.view', 'convenio-claims.create', 'convenio-claims.edit', 'convenio-claims.delete',

            // Triage
            'triage.view', 'triage.create', 'triage.edit', 'triage.delete',

            // Comissoes
            'commissions.view', 'commissions.create', 'commissions.edit', 'commissions.delete',

            // Conciliacao bancaria
            'bank-reconciliation.view', 'bank-reconciliation.create', 'bank-reconciliation.edit', 'bank-reconciliation.delete',

            // Chat
            'chat.view', 'chat.create', 'chat.edit', 'chat.delete',

            // Agenda
            'staff-schedules.view', 'staff-schedules.create', 'staff-schedules.edit', 'staff-schedules.delete',
            'schedules-on-call.view', 'schedules-on-call.create', 'schedules-on-call.edit', 'schedules-on-call.delete',

            // Administracao
            'audit-logs.view', 'audit-logs.delete',
            'backups.view', 'backups.create', 'backups.delete',
            'lab-equipment.view', 'lab-equipment.create', 'lab-equipment.edit', 'lab-equipment.delete',
            'payment-gateways.view', 'payment-gateways.create', 'payment-gateways.edit', 'payment-gateways.delete',
            'configuracoes.view',

            // Purchase Orders
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit', 'purchase-orders.delete',
            'purchase-orders.approve', 'purchase-orders.receive',

            // Drug Formulary
            'drug-formulary.view', 'drug-formulary.create', 'drug-formulary.edit', 'drug-formulary.delete',

            // Stock Transfer
            'stock.transfer',

            // Emergency Protocols
            'emergency-protocols.view', 'emergency-protocols.create', 'emergency-protocols.edit', 'emergency-protocols.delete',

            // Corporate Dashboard
            'corporate-dashboard.view',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ===== DEFINE ROLES AND THEIR PERMISSIONS =====
        $rolePermissions = [
            'super-admin' => $permissions, // all

            'branch-admin' => $permissions, // all within branch scope

            'veterinarian' => [
                'tutors.view', 'tutors.create', 'tutors.edit',
                'pets.view', 'pets.create', 'pets.edit',
                'appointments.view', 'appointments.create', 'appointments.edit',
                'medical-records.view', 'medical-records.create', 'medical-records.edit', 'medical-records.delete',
                'vaccinations.view', 'vaccinations.create', 'vaccinations.edit',
                'exams.view', 'exams.create', 'exams.edit',
                'surgeries.view', 'surgeries.create', 'surgeries.edit',
                'prescriptions.view', 'prescriptions.create', 'prescriptions.edit', 'prescriptions.delete',
                'treatment-plans.view', 'treatment-plans.create', 'treatment-plans.edit',
                'consent-forms.view', 'consent-forms.create', 'consent-forms.edit',
                'dental-charts.view', 'dental-charts.create', 'dental-charts.edit',
                'weight-records.view', 'weight-records.create', 'weight-records.edit',
                'hospitalizations.view', 'hospitalizations.create', 'hospitalizations.edit',
                'anesthesia.view', 'anesthesia.create', 'anesthesia.edit',
                'laboratory.view', 'laboratory.create', 'laboratory.edit',
                'imaging.view', 'imaging.create', 'imaging.edit',
                'controlled-substances.view',
                'boardings.view', 'boardings.create', 'boardings.edit',
                'therapy-sessions.view', 'therapy-sessions.create', 'therapy-sessions.edit',
                'teleconsultations.view', 'teleconsultations.create', 'teleconsultations.edit',
                'referrals.view', 'referrals.create', 'referrals.edit',
                'services.view',
                'convenios.view',
                'vaccine-protocols.view', 'vaccine-protocols.create', 'vaccine-protocols.edit',
                'vaccination-reminders.view', 'vaccination-reminders.create',
                'parasite-controls.view', 'parasite-controls.create', 'parasite-controls.edit',
                'health-certificates.view', 'health-certificates.create', 'health-certificates.edit',
                'drug-interactions.view',
                'clinical-report-templates.view',
                'zoonotic-diseases.view', 'zoonotic-diseases.create', 'zoonotic-diseases.edit',
                'grooming-templates.view',
                'breed-defaults.view',
                'pet-death-records.view', 'pet-death-records.create', 'pet-death-records.edit',
                'pre-anesthetic.view', 'pre-anesthetic.create', 'pre-anesthetic.edit',
                'diet-plans.view', 'diet-plans.create', 'diet-plans.edit',
                'convenio-claims.view', 'convenio-claims.create', 'convenio-claims.edit',
                'triage.view', 'triage.create', 'triage.edit',
                'commissions.view', 'commissions.create', 'commissions.edit',
                'bank-reconciliation.view', 'bank-reconciliation.create',
                'communication.view',
                'notification-logs.view',
                'staff-notes.view', 'staff-notes.create',
                'staff-schedules.view',
                'schedules-on-call.view',
                'online-bookings.view',
                'chat.view', 'chat.create', 'chat.edit', 'chat.delete',
                'drug-formulary.view', 'drug-formulary.create', 'drug-formulary.edit',
                'emergency-protocols.view', 'emergency-protocols.create', 'emergency-protocols.edit', 'emergency-protocols.delete',
            ],

            'receptionist' => [
                'tutors.view', 'tutors.create', 'tutors.edit',
                'pets.view', 'pets.create', 'pets.edit',
                'appointments.view', 'appointments.create', 'appointments.edit',
                'vaccinations.view', 'vaccinations.create',
                'exams.view',
                'surgeries.view',
                'services.view',
                'convenios.view',
                'parasite-controls.view', 'parasite-controls.create',
                'boardings.view', 'boardings.create',
                'communication.view', 'communication.create',
                'notification-logs.view',
                'staff-notes.view', 'staff-notes.create',
                'staff-schedules.view',
                'online-bookings.view', 'online-bookings.create', 'online-bookings.edit',
                'weight-records.view', 'weight-records.create',
                'vaccination-reminders.view',
                'triage.view', 'triage.create',
                'chat.view', 'chat.create',
            ],

            'financial' => [
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                'payments.view', 'payments.create', 'payments.edit',
                'financial-reports.view', 'financial-reports.create',
                'tutors.view',
                'pets.view',
                'appointments.view',
                'convenios.view',
                'payment-gateways.view', 'payment-gateways.create', 'payment-gateways.edit',
                'notification-logs.view',
                'purchase-orders.view',
            ],

            'super-financial' => [
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                'payments.view', 'payments.create', 'payments.edit',
                'financial-reports.view', 'financial-reports.create',
                'tutors.view',
                'pets.view',
                'appointments.view',
                'convenios.view',
                'payment-gateways.view', 'payment-gateways.create', 'payment-gateways.edit',
                'notification-logs.view',
                'branches.view',
                'purchase-orders.view',
                'corporate-dashboard.view',
            ],

            'stock-manager' => [
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'stock.view', 'stock.create', 'stock.edit',
                'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
                'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                'controlled-substances.view', 'controlled-substances.create', 'controlled-substances.edit',
                'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit', 'purchase-orders.delete',
                'purchase-orders.approve', 'purchase-orders.receive',
                'drug-formulary.view', 'drug-formulary.create', 'drug-formulary.edit', 'drug-formulary.delete',
                'stock.transfer',
                'emergency-protocols.view', 'emergency-protocols.create', 'emergency-protocols.edit',
                'notification-logs.view',
            ],

            'human-resources' => [
                'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
                'positions.view', 'positions.create', 'positions.edit', 'positions.delete',
                'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                'users.view',
                'staff-schedules.view',
                'schedules-on-call.view',
            ],

            'tutor' => [
                // Portal access only — no admin permissions
            ],

            'auditor' => [],
        ];

        // Add all view-only permissions to auditor
        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.view') || str_ends_with($perm, '.delete')) {
                $rolePermissions['auditor'][] = $perm;
            }
        }

        // Create/update roles and assign permissions
        foreach ($rolePermissions as $slug => $perms) {
            $role = SpatieRole::findOrCreate($slug, 'web');
            $role->syncPermissions($perms);
        }
    }
}

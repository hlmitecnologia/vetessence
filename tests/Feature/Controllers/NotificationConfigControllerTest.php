<?php

namespace Tests\Feature\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationConfigControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seedAllPermissions();
    }

    private function seedAllPermissions(): void
    {
        $permissions = [
            'admin.view', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'branches.view', 'branches.create', 'branches.edit', 'branches.delete',
            'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            'positions.view', 'positions.create', 'positions.edit', 'positions.delete',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'tutors.view', 'tutors.create', 'tutors.edit', 'tutors.delete',
            'pets.view', 'pets.create', 'pets.edit', 'pets.delete',
            'convenios.view', 'convenios.create', 'convenios.edit', 'convenios.delete',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
            'online-bookings.view', 'online-bookings.create', 'online-bookings.edit', 'online-bookings.delete',
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
            'hospitalizations.view', 'hospitalizations.create', 'hospitalizations.edit', 'hospitalizations.delete',
            'anesthesia.view', 'anesthesia.create', 'anesthesia.edit', 'anesthesia.delete',
            'laboratory.view', 'laboratory.create', 'laboratory.edit', 'laboratory.delete',
            'imaging.view', 'imaging.create', 'imaging.edit', 'imaging.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'stock.view', 'stock.create', 'stock.edit', 'stock.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'controlled-substances.view', 'controlled-substances.create', 'controlled-substances.edit', 'controlled-substances.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            'financial-reports.view', 'financial-reports.create', 'financial-reports.delete',
            'boardings.view', 'boardings.create', 'boardings.edit', 'boardings.delete',
            'therapy-sessions.view', 'therapy-sessions.create', 'therapy-sessions.edit', 'therapy-sessions.delete',
            'services.view', 'services.create', 'services.edit', 'services.delete',
            'grooming-templates.view', 'grooming-templates.create', 'grooming-templates.edit', 'grooming-templates.delete',
            'breed-defaults.view', 'breed-defaults.create', 'breed-defaults.edit', 'breed-defaults.delete',
            'pet-death-records.view', 'pet-death-records.create', 'pet-death-records.edit', 'pet-death-records.delete',
            'communication.view', 'communication.create', 'communication.edit', 'communication.delete',
            'notification-logs.view', 'notification-logs.delete',
            'staff-notes.view', 'staff-notes.create', 'staff-notes.edit', 'staff-notes.delete',
            'teleconsultations.view', 'teleconsultations.create', 'teleconsultations.edit', 'teleconsultations.delete',
            'referrals.view', 'referrals.create', 'referrals.edit', 'referrals.delete',
            'pre-anesthetic.view', 'pre-anesthetic.create', 'pre-anesthetic.edit', 'pre-anesthetic.delete',
            'diet-plans.view', 'diet-plans.create', 'diet-plans.edit', 'diet-plans.delete',
            'convenio-claims.view', 'convenio-claims.create', 'convenio-claims.edit', 'convenio-claims.delete',
            'triage.view', 'triage.create', 'triage.edit', 'triage.delete',
            'commissions.view', 'commissions.create', 'commissions.edit', 'commissions.delete',
            'bank-reconciliation.view', 'bank-reconciliation.create', 'bank-reconciliation.edit', 'bank-reconciliation.delete',
            'chat.view', 'chat.create', 'chat.edit', 'chat.delete',
            'staff-schedules.view', 'staff-schedules.create', 'staff-schedules.edit', 'staff-schedules.delete',
            'schedules-on-call.view', 'schedules-on-call.create', 'schedules-on-call.edit', 'schedules-on-call.delete',
            'audit-logs.view', 'audit-logs.delete',
            'backups.view', 'backups.create', 'backups.delete',
            'lab-equipment.view', 'lab-equipment.create', 'lab-equipment.edit', 'lab-equipment.delete',
            'payment-gateways.view', 'payment-gateways.create', 'payment-gateways.edit', 'payment-gateways.delete',
            'configuracoes.view', 'configuracoes.branding',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit', 'purchase-orders.delete',
            'purchase-orders.approve', 'purchase-orders.receive',
            'drug-formulary.view', 'drug-formulary.create', 'drug-formulary.edit', 'drug-formulary.delete',
            'stock.transfer',
            'emergency-protocols.view', 'emergency-protocols.create', 'emergency-protocols.edit', 'emergency-protocols.delete',
            'corporate-dashboard.view',
            'system-update',
            'docs.view',
            'nfse.view', 'nfse.emit', 'nfse.cancel',
            'nfse-config.edit',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
            );
        }
    }

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(
            ['name' => 'configuracoes.view', 'guard_name' => 'web'],
        );
        $role = Role::firstOrCreate(
            ['name' => 'notificacoes-test', 'guard_name' => 'web'],
            ['slug' => 'notificacoes-test'],
        );
        $role->givePermissionTo($permission);
        $user->assignRole($role);
        return $user;
    }

    public function test_index_returns_form()
    {
        $this->withoutExceptionHandling();

        $user = $this->adminUser();

        Setting::set('notification_email_provider', 'smtp');
        Setting::set('notification_whatsapp_provider', 'zapi');

        $response = $this->actingAs($user)->get(route('configuracoes.notificacoes.index'));
        $response->assertOk();
    }

    public function test_update_saves_smtp_config()
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->put(route('configuracoes.notificacoes.update'), [
            'email_provider' => 'smtp',
            'email_smtp_host' => 'smtp.example.com',
            'email_smtp_port' => '587',
            'email_smtp_username' => 'user',
            'email_smtp_password' => 'pass',
            'email_smtp_encryption' => 'tls',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('smtp', Setting::get('notification_email_provider'));
        $this->assertEquals('smtp.example.com', Setting::get('notification_email_smtp_host'));
    }

    public function test_update_saves_whatsapp_zapi_config()
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->put(route('configuracoes.notificacoes.update'), [
            'whatsapp_provider' => 'zapi',
            'whatsapp_zapi_url' => 'https://api.z-api.io/v1',
            'whatsapp_zapi_token' => 'token-123',
            'whatsapp_zapi_instance' => 'inst-1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('zapi', Setting::get('notification_whatsapp_provider'));
        $this->assertEquals('token-123', Setting::get('notification_whatsapp_zapi_token'));
    }

    public function test_update_validates_provider_values()
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->put(route('configuracoes.notificacoes.update'), [
            'email_provider' => 'invalid_provider',
        ]);

        $response->assertSessionHasErrors(['email_provider']);
    }
}

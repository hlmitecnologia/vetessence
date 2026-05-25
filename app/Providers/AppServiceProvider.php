<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use App\Rules\Cpf;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            return (new Cpf)->passes($attribute, $value);
        });

        $this->registerGates();
    }

    protected function registerGates()
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(['super-admin', 'admin'])) {
                return true;
            }
        });

        // Admin
        Gate::define('admin', fn($user) => $user->hasPermissionTo('admin.view'));
        Gate::define('unidades', fn($user) => $user->hasPermissionTo('branches.view'));
        Gate::define('configuracoes', fn($user) => $user->hasPermissionTo('configuracoes.view'));

        // HR
        Gate::define('departments.view', fn($user) => $user->hasPermissionTo('departments.view'));
        Gate::define('positions.view', fn($user) => $user->hasPermissionTo('positions.view'));
        Gate::define('employees.view', fn($user) => $user->hasPermissionTo('employees.view'));

        // Cadastro
        Gate::define('tutores', fn($user) => $user->hasPermissionTo('tutors.view'));
        Gate::define('pets', fn($user) => $user->hasPermissionTo('pets.view'));
        Gate::define('convenios', fn($user) => $user->hasPermissionTo('convenios.view'));

        // Atendimento
        Gate::define('atendimentos', fn($user) => $user->hasPermissionTo('appointments.view'));
        Gate::define('prontuarios', fn($user) => $user->hasPermissionTo('medical-records.view'));
        Gate::define('vacinas', fn($user) => $user->hasPermissionTo('vaccinations.view'));
        Gate::define('exames', fn($user) => $user->hasPermissionTo('exams.view'));
        Gate::define('cirurgias', fn($user) => $user->hasPermissionTo('surgeries.view'));
        Gate::define('prescricoes', fn($user) => $user->hasPermissionTo('prescriptions.view'));
        Gate::define('hospitalizacao', fn($user) => $user->hasPermissionTo('hospitalizations.view'));
        Gate::define('laboratorio', fn($user) => $user->hasPermissionTo('laboratory.view'));
        Gate::define('imagem', fn($user) => $user->hasPermissionTo('imaging.view'));
        Gate::define('referral', fn($user) => $user->hasPermissionTo('referrals.view'));
        Gate::define('parasitario', fn($user) => $user->hasPermissionTo('parasite-controls.view'));
        Gate::define('protocolo-vacinas', fn($user) => $user->hasPermissionTo('vaccine-protocols.view'));
        Gate::define('lembrete-vacinas', fn($user) => $user->hasPermissionTo('vaccination-reminders.view'));
        Gate::define('teleconsulta', fn($user) => $user->hasPermissionTo('teleconsultations.view'));
        Gate::define('agendamento-online', fn($user) => $user->hasPermissionTo('online-bookings.view'));
        Gate::define('interacao-medicamentosa', fn($user) => $user->hasPermissionTo('drug-interactions.view'));
        Gate::define('modelo-laudo', fn($user) => $user->hasPermissionTo('clinical-report-templates.view'));
        Gate::define('certificado-sanitario', fn($user) => $user->hasPermissionTo('health-certificates.view'));
        Gate::define('obito', fn($user) => $user->hasPermissionTo('pet-death-records.view'));
        Gate::define('servicos', fn($user) => $user->hasPermissionTo('services.view'));
        Gate::define('terapias', fn($user) => $user->hasPermissionTo('therapy-sessions.view'));
        Gate::define('hospedagem', fn($user) => $user->hasPermissionTo('boardings.view'));
        Gate::define('zoonoses', fn($user) => $user->hasPermissionTo('zoonotic-diseases.view'));

        // Financeiro
        Gate::define('financeiro', fn($user) => $user->hasPermissionTo('invoices.view'));
        Gate::define('gateway-pagamento', fn($user) => $user->hasPermissionTo('payment-gateways.view'));

        // Estoque
        Gate::define('estoque', fn($user) => $user->hasPermissionTo('products.view'));
        Gate::define('purchase-orders.view', fn($user) => $user->hasPermissionTo('purchase-orders.view'));
        Gate::define('purchase-orders.create', fn($user) => $user->hasPermissionTo('purchase-orders.create'));
        Gate::define('purchase-orders.edit', fn($user) => $user->hasPermissionTo('purchase-orders.edit'));
        Gate::define('purchase-orders.delete', fn($user) => $user->hasPermissionTo('purchase-orders.delete'));
        Gate::define('purchase-orders.approve', fn($user) => $user->hasPermissionTo('purchase-orders.approve'));
        Gate::define('purchase-orders.receive', fn($user) => $user->hasPermissionTo('purchase-orders.receive'));

        // Fase T
        Gate::define('drug-formulary.view', fn($user) => $user->hasPermissionTo('drug-formulary.view'));
        Gate::define('drug-formulary.create', fn($user) => $user->hasPermissionTo('drug-formulary.create'));
        Gate::define('drug-formulary.edit', fn($user) => $user->hasPermissionTo('drug-formulary.edit'));
        Gate::define('drug-formulary.delete', fn($user) => $user->hasPermissionTo('drug-formulary.delete'));
        Gate::define('stock.transfer', fn($user) => $user->hasPermissionTo('stock.transfer'));
        Gate::define('emergency-protocols.view', fn($user) => $user->hasPermissionTo('emergency-protocols.view'));
        Gate::define('emergency-protocols.create', fn($user) => $user->hasPermissionTo('emergency-protocols.create'));
        Gate::define('emergency-protocols.edit', fn($user) => $user->hasPermissionTo('emergency-protocols.edit'));
        Gate::define('emergency-protocols.delete', fn($user) => $user->hasPermissionTo('emergency-protocols.delete'));
        Gate::define('corporate-dashboard.view', fn($user) => $user->hasPermissionTo('corporate-dashboard.view'));
        Gate::define('system-update', fn($user) => $user->hasPermissionTo('system-update'));
        Gate::define('configuracoes.branding', fn($user) => $user->hasPermissionTo('configuracoes.branding'));
        Gate::define('docs.view', fn($user) => $user->hasPermissionTo('docs.view'));

        // Equipamentos
        Gate::define('integracao-equipamentos', fn($user) => $user->hasPermissionTo('lab-equipment.view'));

        // Comunicacao
        Gate::define('notificacoes', fn($user) => $user->hasPermissionTo('notification-logs.view'));
        Gate::define('nota-interna', fn($user) => $user->hasPermissionTo('staff-notes.view'));
        Gate::define('chat', fn($user) => $user->hasPermissionTo('chat.view'));

        // Agenda
        Gate::define('agenda-equipe', fn($user) => $user->hasPermissionTo('staff-schedules.view'));

        // Auditoria
        Gate::define('auditoria', fn($user) => $user->hasPermissionTo('audit-logs.view'));

        // Backup
        Gate::define('backup', fn($user) => $user->hasPermissionTo('backups.view'));

        // NFSe
        Gate::define('nfse.view', fn($user) => $user->hasPermissionTo('nfse.view'));
        Gate::define('nfse.emit', fn($user) => $user->hasPermissionTo('nfse.emit'));
        Gate::define('nfse.cancel', fn($user) => $user->hasPermissionTo('nfse.cancel'));
        Gate::define('nfse-config.edit', fn($user) => $user->hasPermissionTo('nfse-config.edit'));
    }
}

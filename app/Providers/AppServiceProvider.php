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
        //
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
        Gate::define('admin', fn($user) => $user->can('admin.view'));
        Gate::define('unidades', fn($user) => $user->can('branches.view'));
        Gate::define('configuracoes', fn($user) => $user->can('configuracoes.view'));

        // HR
        Gate::define('departments.view', fn($user) => $user->can('departments.view'));
        Gate::define('positions.view', fn($user) => $user->can('positions.view'));
        Gate::define('employees.view', fn($user) => $user->can('employees.view'));

        // Cadastro
        Gate::define('tutores', fn($user) => $user->can('tutors.view'));
        Gate::define('pets', fn($user) => $user->can('pets.view'));
        Gate::define('convenios', fn($user) => $user->can('convenios.view'));

        // Atendimento
        Gate::define('atendimentos', fn($user) => $user->can('appointments.view'));
        Gate::define('prontuarios', fn($user) => $user->can('medical-records.view'));
        Gate::define('vacinas', fn($user) => $user->can('vaccinations.view'));
        Gate::define('exames', fn($user) => $user->can('exams.view'));
        Gate::define('cirurgias', fn($user) => $user->can('surgeries.view'));
        Gate::define('prescricoes', fn($user) => $user->can('prescriptions.view'));
        Gate::define('hospitalizacao', fn($user) => $user->can('hospitalizations.view'));
        Gate::define('laboratorio', fn($user) => $user->can('laboratory.view'));
        Gate::define('imagem', fn($user) => $user->can('imaging.view'));
        Gate::define('referral', fn($user) => $user->can('referrals.view'));
        Gate::define('parasitario', fn($user) => $user->can('parasite-controls.view'));
        Gate::define('protocolo-vacinas', fn($user) => $user->can('vaccine-protocols.view'));
        Gate::define('lembrete-vacinas', fn($user) => $user->can('vaccination-reminders.view'));
        Gate::define('teleconsulta', fn($user) => $user->can('teleconsultations.view'));
        Gate::define('agendamento-online', fn($user) => $user->can('online-bookings.view'));
        Gate::define('interacao-medicamentosa', fn($user) => $user->can('drug-interactions.view'));
        Gate::define('modelo-laudo', fn($user) => $user->can('clinical-report-templates.view'));
        Gate::define('certificado-sanitario', fn($user) => $user->can('health-certificates.view'));
        Gate::define('obito', fn($user) => $user->can('pet-death-records.view'));
        Gate::define('servicos', fn($user) => $user->can('services.view'));
        Gate::define('terapias', fn($user) => $user->can('therapy-sessions.view'));
        Gate::define('hospedagem', fn($user) => $user->can('boardings.view'));
        Gate::define('zoonoses', fn($user) => $user->can('zoonotic-diseases.view'));

        // Financeiro
        Gate::define('financeiro', fn($user) => $user->can('invoices.view'));
        Gate::define('gateway-pagamento', fn($user) => $user->can('payment-gateways.view'));

        // Estoque
        Gate::define('estoque', fn($user) => $user->can('products.view'));
        Gate::define('purchase-orders.view', fn($user) => $user->can('purchase-orders.view'));
        Gate::define('purchase-orders.create', fn($user) => $user->can('purchase-orders.create'));
        Gate::define('purchase-orders.edit', fn($user) => $user->can('purchase-orders.edit'));
        Gate::define('purchase-orders.delete', fn($user) => $user->can('purchase-orders.delete'));
        Gate::define('purchase-orders.approve', fn($user) => $user->can('purchase-orders.approve'));
        Gate::define('purchase-orders.receive', fn($user) => $user->can('purchase-orders.receive'));

        // Fase T
        Gate::define('drug-formulary.view', fn($user) => $user->can('drug-formulary.view'));
        Gate::define('drug-formulary.create', fn($user) => $user->can('drug-formulary.create'));
        Gate::define('drug-formulary.edit', fn($user) => $user->can('drug-formulary.edit'));
        Gate::define('drug-formulary.delete', fn($user) => $user->can('drug-formulary.delete'));
        Gate::define('stock.transfer', fn($user) => $user->can('stock.transfer'));
        Gate::define('emergency-protocols.view', fn($user) => $user->can('emergency-protocols.view'));
        Gate::define('emergency-protocols.create', fn($user) => $user->can('emergency-protocols.create'));
        Gate::define('emergency-protocols.edit', fn($user) => $user->can('emergency-protocols.edit'));
        Gate::define('emergency-protocols.delete', fn($user) => $user->can('emergency-protocols.delete'));
        Gate::define('corporate-dashboard.view', fn($user) => $user->can('corporate-dashboard.view'));

        // Equipamentos
        Gate::define('integracao-equipamentos', fn($user) => $user->can('lab-equipment.view'));

        // Comunicacao
        Gate::define('notificacoes', fn($user) => $user->can('notification-logs.view'));
        Gate::define('nota-interna', fn($user) => $user->can('staff-notes.view'));
        Gate::define('chat', fn($user) => $user->can('chat.view'));

        // Agenda
        Gate::define('agenda-equipe', fn($user) => $user->can('staff-schedules.view'));

        // Auditoria
        Gate::define('auditoria', fn($user) => $user->can('audit-logs.view'));

        // Backup
        Gate::define('backup', fn($user) => $user->can('backups.view'));
    }
}

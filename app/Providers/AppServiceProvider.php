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

        Gate::define('admin', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('tutores', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('pets', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('atendimentos', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('prontuarios', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('vacinas', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('exames', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('cirurgias', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('financeiro', function ($user) {
            return $user->hasRole(['admin', 'financeiro', 'super-financial']);
        });

        Gate::define('estoque', function ($user) {
            return $user->hasRole(['admin', 'estoque']);
        });

        Gate::define('convenios', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('prescricoes', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('zoonoses', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('hospitalizacao', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('laboratorio', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('imagem', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('referral', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('parasitario', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('protocolo-vacinas', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('lembrete-vacinas', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('teleconsulta', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('unidades', function ($user) {
            return $user->hasRole(['admin']);
        });

        Gate::define('gateway-pagamento', function ($user) {
            return $user->hasRole(['admin', 'financeiro', 'super-financial']);
        });

        Gate::define('integracao-equipamentos', function ($user) {
            return $user->hasRole(['admin']);
        });

        Gate::define('agendamento-online', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('nota-interna', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('hospedagem', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('interacao-medicamentosa', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('modelo-laudo', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('certificado-sanitario', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('notificacoes', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('obito', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('servicos', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('terapias', function ($user) {
            return $user->hasRole(['admin', 'veterinario']);
        });

        Gate::define('configuracoes', function ($user) {
            return $user->hasRole(['admin']);
        });

        Gate::define('agenda-equipe', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });

        Gate::define('auditoria', function ($user) {
            return $user->hasRole(['admin']);
        });

        Gate::define('backup', function ($user) {
            return $user->hasRole(['admin']);
        });
    }
}

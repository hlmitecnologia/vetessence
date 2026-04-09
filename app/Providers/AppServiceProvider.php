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
            if ($user->hasRole('admin')) {
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
            return $user->hasRole(['admin', 'financeiro']);
        });

        Gate::define('estoque', function ($user) {
            return $user->hasRole(['admin', 'estoque']);
        });

        Gate::define('convenios', function ($user) {
            return $user->hasRole(['admin', 'veterinario', 'recepcionista']);
        });
    }
}

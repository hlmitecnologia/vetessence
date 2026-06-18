<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\View\ViewException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->route() && $e instanceof ViewException && str_contains($e->getMessage(), 'not found')) {
            $routeName = $request->route()->getName();

            if (preg_match('/\.(create|edit)$/', (string) $routeName)) {
                $baseRoute = preg_replace('/\.(create|edit)$/', '.index', (string) $routeName);

                if (Route::has($baseRoute)) {
                    return redirect()->route($baseRoute);
                }
            }
        }

        if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
            return redirect()->route('dashboard')
                ->with('error', 'Acesso negado. Você não tem permissão para acessar esta funcionalidade.');
        }

        return parent::render($request, $e);
    }
}

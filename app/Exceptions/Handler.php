<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\View\ViewException;
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

        return parent::render($request, $e);
    }
}

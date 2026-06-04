<?php

namespace App\Http\Middleware;

use App\Models\Tutor;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'tutor') {
                    return redirect()->route('portal.dashboard');
                }
                if (Auth::guard($guard)->user()->hasRole('tutor')) {
                    $tutor = Tutor::where('user_id', Auth::guard($guard)->id())->first();
                    if ($tutor) {
                        Auth::guard('tutor')->login($tutor);
                    }
                    return redirect()->route('portal.dashboard');
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}

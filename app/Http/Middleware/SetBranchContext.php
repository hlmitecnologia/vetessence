<?php

namespace App\Http\Middleware;

use App\Services\BranchContext;
use Closure;
use Illuminate\Http\Request;

class SetBranchContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            BranchContext::set($user->branch_id);
        }

        return $next($request);
    }
}

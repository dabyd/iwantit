<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        if ($request->session()->get('two_factor.confirmed')) {
            return $next($request);
        }

        return redirect()->route('two-factor.challenge');
    }
}

<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;

class AuthenticateLogin
{
    public function handle($request, Closure $next)
    {
        if (! Sentinel::getUser()) {
            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}

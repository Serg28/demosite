<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;

class AuthenticateLoginToHome
{
    public function handle($request, Closure $next)
    {
        if (! Sentinel::getUser()) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}

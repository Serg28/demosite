<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateProvider
{
    public function handle(Request $request, Closure $next): Response
    {
        $provider = $request->route('provider');
        $allowed = array_keys(array_filter(config('auth_features.socialite', [])));

        if (! in_array($provider, $allowed, true)) {
            abort(404);
        }

        return $next($request);
    }
}

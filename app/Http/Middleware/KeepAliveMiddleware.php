<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class KeepAliveMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Keep-Alive', 'timeout=15');
        $response->header('Connection', 'Keep-Alive');

        return $response;
    }
}

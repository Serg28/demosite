<?php

namespace App\Http\Middleware;

use Closure;

class ServerPushMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response->headers->get('content-type') === 'text/css') {
            $response->header('link', '</css/main.min.css>; rel=preload; as=style');
        }
        return $response;
    }
}

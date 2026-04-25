<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckForValidPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $page = $request->query('page');

        if (isset($page) && (!is_numeric($page) || $page < 1)) {
            abort(404);
        }

        return $next($request);
    }
}

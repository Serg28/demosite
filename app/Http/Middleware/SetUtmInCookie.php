<?php

namespace App\Http\Middleware;

use App\Services\UtmLabel;
use Closure;
use Illuminate\Http\Request;

class SetUtmInCookie
{
    private UtmLabel $utmLabel;

    public function __construct(UtmLabel $utmLabel)
    {
        $this->utmLabel = $utmLabel;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->utmLabel->saveCookies();

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Helpers\PhoneNumberHelper;
use Closure;
use Illuminate\Http\Request;

class NormalizePhoneNumberUa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        if ($input && !empty($input['phone'])) {
            $input['phone'] = PhoneNumberHelper::formatPhoneNumber($input['phone']);
            $request->replace($input);
            unset($input);
        }

        return $next($request);
    }
}

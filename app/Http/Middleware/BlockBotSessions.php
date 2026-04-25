<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class BlockBotSessions
{
    public function handle($request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');

        // Проверка наличия строки "bot" в User-Agent
        //if (str_contains($userAgent, 'bot')) {
        // Проверка наличия строки "bot" в User-Agent, но исключаем "facebook", "facebot", "meta"
        if (str_contains($userAgent, 'bot')
            && !str_contains($userAgent, 'facebook')
            && !str_contains($userAgent, 'facebot')
            && !str_contains($userAgent, 'Facebot')
            && !str_contains($userAgent, 'meta')) {

            // Блокировка сессии для ботов
            //session()->flush(); // Очистка текущей сессии
            //return response('Blocked for bots', 403); // Опционально: вернуть запретный ответ
            \Config::set('session.driver', 'cookie');
            //Log::info($request->header('User-Agent'));
        }

        return $next($request);
    }
}

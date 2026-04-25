<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LanguageRedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        // Получаем путь из URL
        $path = $request->getPathInfo();

        //dd($path, geturl($path, 'ru'));

        //Получить текущую модель? и определить, какой у нее текущий слаг/урл.
        //Если все ок, завершаем
        //Если неправильный, то создаем правильный и редиректим на него.

        // Проверяем, является ли путь корректным для текущего языка
        //if (!$this->isValidPathForLocale($path, $currentLocale)) {
        // Получаем правильный URL для текущего языка
        $correctUrl = LaravelLocalization::getLocalizedURL($currentLocale, $path);

        // Выполняем 301 редирект на правильный URL
        //return Redirect::to($correctUrl, 301);
        //}

        //return $next($request);
    }

    protected function isValidPathForLocale($path, $locale)
    {
        // Проверяем, содержит ли путь правильный префикс языка
        //return preg_match('/^\/' . preg_quote($locale, '/') . '(\/|$)/', $path);
    }
}

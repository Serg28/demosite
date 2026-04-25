<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class CheckValidLanguage
{
    public function handle($request, Closure $next)
    {
        $validLanguages = getSiteLanguages()->all();
        $currentLanguage = App::getLocale();

        // Проверяем, есть ли текущий язык в списке допустимых языков
        if (!in_array($currentLanguage, $validLanguages, true)) {
            // Если нет, возвращаем ошибку 404
            //abort(404);
            //return redirect(geturl(defaultLanguage()), 301);
            //return Redirect::to(geturl(defaultLanguage()), 301);
            App::setLocale(defaultLanguage());
            abort(404);
        }

        return $next($request);
    }
}

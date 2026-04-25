<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vis\Builder\Models\Language;

class SetUserLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasCookie('is_check_locale') && ! $request->is('admin/*') && ! $request->is('sitemap.xml')) {
            return redirect()->to(geturl($request->path(), $this->getLocaleBrowser()))
                ->cookie(cookie('is_check_locale', '1', 36000));
        }

        return $next($request);
    }

    private function getLocaleBrowser(): string
    {
        $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $languages = (new Language())->getLanguages()->pluck('language')->toArray();

        if (! in_array($browserLanguage, $languages)) {
            $browserLanguage = defaultLanguage();
        }

        return $browserLanguage;
    }
}

<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Vis\Builder\Models\Language;

class LanguagesComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('LanguagesComposer', 'Time for LanguagesComposer');
        $languages = (new Language())->getLanguages()->pluck('language');
        $count = count($languages);

        $currentLang = App::getLocale();

        $lang = $currentLang === defaultLanguage() ? '/' : '/'.$currentLang.'/';

        $view->with(compact('languages', 'currentLang', 'lang', 'count'));
        debugbar()->stopMeasure('LanguagesComposer');
    }
}

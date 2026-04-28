<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Linecore\Cms\Models\Language;

class SeoComposer
{
    public function compose(View $view): void
    {
        $languages = Cache::tags(['languages'])->remember('seo_active_languages', 3600, function () {
            return Language::where('is_active', 1)->orderBy('priority')->pluck('language');
        });

        $view->with(compact('languages'));
    }
}

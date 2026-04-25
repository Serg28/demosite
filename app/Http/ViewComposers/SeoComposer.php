<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Vis\Builder\Models\Language;

class SeoComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('SeoComposer', 'Time for SeoComposer');
        $languages = (new Language())->getLanguages()->pluck('language');

        $view->with(compact('languages'));
        debugbar()->stopMeasure('SeoComposer');
    }
}

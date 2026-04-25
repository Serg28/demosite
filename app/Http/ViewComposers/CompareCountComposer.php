<?php

namespace App\Http\ViewComposers;

use App\Services\Compare;
use Illuminate\View\View;

class CompareCountComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('CompareCountComposer', 'Time for CompareCountComposer');
        $compareCount = (new Compare())->count();
        $view->with(compact('compareCount'));
        debugbar()->stopMeasure('CompareCountComposer');
    }
}

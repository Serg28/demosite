<?php

namespace App\Http\ViewComposers;

use App\Models\CharacteristicOption;
use Illuminate\View\View;

class FilterSelectComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('FilterSelectComposer', 'Time for FilterSelectComposer');
        $filters = CharacteristicOption::leftJoin('characteristics', 'characteristics.id', '=', 'characteristic_options.characteristic_id')
            ->whereIn('characteristic_options.slug', $view->filters)
            ->where('characteristics.slug', $view->category)
            ->select('characteristic_options.*')
            ->rememberForever()->cacheTags(['characteristic_options'])
            ->get();

        $view->with(compact('filters'));
        debugbar()->stopMeasure('FilterSelectComposer');
    }
}

<?php

namespace App\Http\ViewComposers;

use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use Illuminate\View\View;

class CartFiltersComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('CartFiltersComposer', 'Time for CartFiltersComposer');
        $filters = [];

        if (count($view->product->options)) {
            foreach ($view->product->options as $characteristic => $option) {
                $characteristicModel = Characteristic::find($characteristic);
                $optionModel = CharacteristicOption::find($option);

                if ($characteristicModel && $optionModel) {
                    $filters[$characteristicModel->t('title')] = $optionModel->t('title');
                }
            }
        }

        $view->with(compact('filters'));
        debugbar()->stopMeasure('CartFiltersComposer');
    }
}

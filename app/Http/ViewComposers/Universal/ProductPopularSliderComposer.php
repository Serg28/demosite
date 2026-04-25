<?php

namespace App\Http\ViewComposers\Universal;

use Illuminate\View\View;

class ProductPopularSliderComposer
{
    public function compose(View $view): void
    {
        $topProducts = $view->block->popularProducts()->active()->rememberForever()->cacheTags(['tree', 'products'])->get();

        $view->with(compact('topProducts'));
    }
}

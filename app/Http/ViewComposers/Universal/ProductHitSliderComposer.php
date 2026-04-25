<?php

namespace App\Http\ViewComposers\Universal;

use Illuminate\View\View;

class ProductHitSliderComposer
{
    public function compose(View $view): void
    {
        $hitsProducts = $view->block->hitProducts()->active()->rememberForever()->cacheTags(['tree', 'products'])->get();

        $view->with(compact('hitsProducts'));
    }
}

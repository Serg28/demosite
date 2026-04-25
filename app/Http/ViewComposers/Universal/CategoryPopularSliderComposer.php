<?php

namespace App\Http\ViewComposers\Universal;

use Illuminate\View\View;

class CategoryPopularSliderComposer
{
    public function compose(View $view): void
    {
        $topCategories = $view->block->popularCategories()->active()->rememberForever()->cacheTags(['tree', 'categories'])->get();

        $view->with(compact('topCategories'));
    }
}

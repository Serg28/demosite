<?php

namespace App\Http\ViewComposers\Home;

use App\Models\Brand;
use Illuminate\View\View;

class BrandsComposer
{
    public function compose(View $view): void
    {
        $brands = Brand::active()->orderPriority()
            ->rememberForever()->cacheTags(['brands'])
            ->get();

        $view->with(compact('brands'));
    }
}

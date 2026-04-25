<?php

namespace App\Http\ViewComposers\Universal;

use Illuminate\View\View;

class BannerSliderComposer
{
    public function compose(View $view): void
    {
        $sliders = $view->block->bannersSlider()->active()->rememberForever()->cacheTags(['tree', 'block_banners_slider'])->get();

        $view->with(compact('sliders'));
    }
}

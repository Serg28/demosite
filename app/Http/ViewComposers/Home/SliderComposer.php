<?php

namespace App\Http\ViewComposers\Home;

use App\Models\SliderMain;
use Illuminate\View\View;

class SliderComposer
{
    public function compose(View $view): void
    {
        $sliders = SliderMain::active()->orderPriority()
            ->rememberForever()->cacheTags(['slider_main'])
            ->get();

        $view->with(compact('sliders'));
    }
}

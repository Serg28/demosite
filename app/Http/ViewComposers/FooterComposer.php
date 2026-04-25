<?php

namespace App\Http\ViewComposers;

use App\Models\MenuFooter;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('FooterComposer', 'Time for FooterComposer');
        $menu = (new MenuFooter())->getMenu();
        $view->with(compact('menu'));
        debugbar()->stopMeasure('FooterComposer');
    }
}

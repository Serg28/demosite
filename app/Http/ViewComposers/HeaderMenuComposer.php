<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use App\Models\MenuHeader;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class HeaderMenuComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('HeaderMenuComposer', 'Time for HeaderMenuComposer');
        $topMenu = (new MenuHeader())->getMenu();

        //$categories = (new Category())->getMenu();
        $topMenucacheKey = md5($topMenu).'_'.App::getLocale();
        $topMenuTags = ['categories','tb_tree','menu_header'];

        $view->with(compact('topMenu'/*, 'categories'*/,'topMenucacheKey', 'topMenuTags'));
        debugbar()->stopMeasure('HeaderMenuComposer');
    }
}

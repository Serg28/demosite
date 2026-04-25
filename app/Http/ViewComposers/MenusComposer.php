<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use App\Models\MenuFooter;
use App\Models\MenuHeader;
use App\Models\MenuSeocatalog;
use Illuminate\View\View;

class MenusComposer
{
    public function compose(View $view): void
    {
        //$topMenu = (new MenuHeader())->getMenu();
        //$bottomMenu = (new MenuFooter())->getMenu();
        //$seocatalogMenu = (new MenuSeocatalog())->getMenu();
        //$categories = (new Category())->getMenu();

        //$view->with(compact('topMenu'/*, 'categories', 'bottomMenu'*//*, 'seocatalogMenu'*/));
    }
}

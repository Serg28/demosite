<?php

namespace App\Http\ViewComposers;

use App\Models\Tree;
use App\Services\Breadcrumbs;
use  Illuminate\View\View;

class BreadcrumbsSimpleComposer
{
    public function compose(View $view): void
    {
        $node = Tree::find(1);
        $breadcrumbs = new Breadcrumbs($node);
        $breadcrumbs->add('/', $view->page);

        $view->with(compact('breadcrumbs'));
    }
}

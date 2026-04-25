<?php

namespace App\Http\ViewComposers;

use App\Models\Tree;
use App\Services\Breadcrumbs;
use  Illuminate\View\View;

class BreadcrumbsOptionsComposer
{
    public function compose(View $view): void
    {
        $node = Tree::find(1);
        $breadcrumbs = new Breadcrumbs($node);
        $breadcrumbs->add($view->page->characteristic->getUrl(), $view->page->characteristic->t('title'));
        $breadcrumbs->add('/', $view->page->t('title'));

        $view->with(compact('breadcrumbs'));
    }
}

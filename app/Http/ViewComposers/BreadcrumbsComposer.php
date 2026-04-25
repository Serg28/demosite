<?php

namespace App\Http\ViewComposers;

use App\Services\Breadcrumbs;
use  Illuminate\View\View;

class BreadcrumbsComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('BreadcrumbsComposer', 'Time for BreadcrumbsComposer');
        $node = $view->page->getNode();

        $breadcrumbs = new Breadcrumbs($node);

        if (!is_subclass_of($view->page, 'Vis\Builder\Tree')) {
            $breadcrumbs->add($view->page->getUrl(), $view->page->t('title'));
        }

        $view->with(compact('breadcrumbs'));
        debugbar()->stopMeasure('BreadcrumbsComposer');
    }
}

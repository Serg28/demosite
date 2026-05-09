<?php

namespace App\Http\ViewComposers;

use App\Services\BreadcrumbsService;
use Illuminate\View\View;

class BreadcrumbsSimpleComposer
{
    public function __construct(private readonly BreadcrumbsService $breadcrumbs) {}

    public function compose(View $view): void
    {
        $pages = $view->getData()['pages'] ?? [];
        $currentTitle = $view->getData()['breadcrumbTitle'] ?? '';

        $view->with('breadcrumbs', $this->breadcrumbs->simple($currentTitle, $pages));
    }
}

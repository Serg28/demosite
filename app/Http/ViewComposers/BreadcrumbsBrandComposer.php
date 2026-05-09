<?php

namespace App\Http\ViewComposers;

use App\Services\BreadcrumbsService;
use Illuminate\View\View;

/**
 * Хлібні крихти для сторінки бренду/виробника.
 * Аналог BreadcrumbsVendorComposer з linecore-demo.
 * View повинен мати: $brandName (string), опційно $brandsUrl (string).
 */
class BreadcrumbsBrandComposer
{
    public function __construct(private readonly BreadcrumbsService $breadcrumbs) {}

    public function compose(View $view): void
    {
        $brandName = $view->getData()['brandName'] ?? '';
        $brandsUrl = $view->getData()['brandsUrl'] ?? null;

        if ($brandName === '') {
            return;
        }

        $view->with('breadcrumbs', $this->breadcrumbs->forBrand($brandName, $brandsUrl));
    }
}

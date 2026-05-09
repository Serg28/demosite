<?php

namespace App\Http\ViewComposers;

use App\Services\BreadcrumbsService;
use Illuminate\View\View;

/**
 * Хлібні крихти для сторінки категорії каталогу.
 * SEO: остання крихта — seo_h1 (без {{pagenumber}}) через getSeoBreadcrumbTitle().
 * Для активного SEO-фільтру — передати $filterTitle у view; буде додана окрема крихта.
 * Аналог BreadcrumbsCategoryComposer з linecore-demo.
 */
class BreadcrumbsCategoryComposer
{
    public function __construct(private readonly BreadcrumbsService $breadcrumbs) {}

    public function compose(View $view): void
    {
        $category = $view->getData()['category'] ?? null;

        if ($category === null) {
            return;
        }

        $filterTitle = $view->getData()['filterTitle'] ?? null;

        $breadcrumbs = $filterTitle
            ? $this->breadcrumbs->forFilteredCategory($category, $filterTitle)
            : $this->breadcrumbs->forCategory($category);

        $view->with('breadcrumbs', $breadcrumbs);
    }
}

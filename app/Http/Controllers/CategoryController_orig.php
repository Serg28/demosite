<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ElasticsearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Vis\Builder\TreeController;

class CategoryController_orig extends TreeController
{
    public function index(Category $page): View|JsonResponse
    {
        $filter = $page->filter()->init();

        $results = (new ElasticsearchService())->filter($page, $filter);

        return $this->getViewResult($page, $filter, $results);
    }

    public function routeCatalog(Category $page): View|JsonResponse
    {
        if (! $page->characteristics()->rememberForever()->cacheTags(['category'])->count()) {
            $filter = $page->filter()->init();
            $products = $page->products()->active()->paginate(12);

            return view(
                'category.index_without_filter',
                compact('page', 'products', 'filter')
            );
        }

        return $this->index($page);
    }

    public function catalog(Category $page): View
    {
        $rubrics = $page->children()->active()->defaultOrder()->get();

        return view('category.catalog', compact('page', 'rubrics'));
    }

    public function changeProductsView(string $view): void
    {
        Cookie::queue('products-view', $view, 5000);
    }

    private function getViewResult(Category $page, $filter, $results): View|JsonResponse
    {
        if (request()->ajax()) {
            if (request()->has('show-more')) {
                return response()->json([
                    'products' => view('category.partials.products', compact('filter', 'results'))->render(),
                    'links' => view('category.partials.paginate', compact('filter', 'results'))->render(),
                ]);
            }

            return response()->json([
                'html' => view('category.partials.center', compact('page', 'results', 'filter'))->render(),
                'filterpopup' => view('category.partials.filter_popup', compact('page', 'results', 'filter'))->render(),
            ]);
        }

        return view(
            'category.index',
            compact('page', 'results', 'filter')
        );
    }

    public function rebuildCatagories()
    {
        Category::fixTree();
    }
}

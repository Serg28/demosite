<?php

namespace App\Http\Controllers;

use App\Http\Facades\LastModified;
use App\Http\Resources\SeoCustomUrlCatalogResource;
use App\Models\Category;
use App\Services\Category as CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Category $page): View|JsonResponse
    {
        $data = $this->categoryService->getElasticsearchData($page);
        return $this->getViewResult(...$data);
    }

    // Страница списка подкатегорий.
    public function catalog(Category $page): View
    {
        //return view('category.catalog', [
        return view($page->getTemplateCatalog(), [
            'page' => $page,
            'rubrics' => $this->categoryService->getRubricsForCategory($page)
        ]);
    }

    // Страница категории - без подкатегорий, список товаров без фильтра.
    public function routeCatalog(Category $page): View
    {
        $result = $this->categoryService->getCatalogProductsView($page);

        if (is_array($result)) {
            //return view('category.index_without_filter', $result);
            return view($page->getTemplateIndexWithoutFilter(), $result);

        }

        return $this->index($page);
    }

    public function changeProductsView(string $view): void
    {
        Cookie::queue('products-view', $view, 5000);
    }

    private function getViewResult(Category $page, $filter, $results): View|JsonResponse
    {
        $count = $results['products']->total();
        $count = $count ?: 0;

        if ($count) {
            $cacheKey = 'category_filter_' . App::getLocale() . '_' . $page->id . '_' . md5(serialize($filter) . serialize($results));
            $filter_cont = Cache::tags(['category_filter'])
                ->remember($cacheKey, now()->addWeek(), function () use ($page, $filter, $results, $count) {
                    return view(
                        'category.partials.filter_cont',
                        compact('page', 'results', 'filter', 'count')
                    )->render();
                });
        } else {
            $filter_cont = '';
        }
        //--

        if (request()->ajax()) {
            LastModified::set(now());

            if (request()->has('show-more')) {
                return response()->json([
                    'products' => view('category.partials.products', compact('filter', 'results'))->render(),
                    'links' => view('category.partials.paginate', compact('filter', 'results'))->render(),
                ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            }

            return response()->json([
                'html' => view(
                    'category.partials.center',
                    compact('page', 'results', 'filter', 'filter_cont', 'count')
                )->render(),
                'filterpopup' => view(
                    'category.partials.filter_popup',
                    compact('page', 'results', 'filter', 'filter_cont', 'count')
                )->render(),
                'seo' => new SeoCustomUrlCatalogResource($page, $filter),
                'count' => $count
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }

        LastModified::set($page->updated_at);

        return view(
            //'category.index',
            $page->getTemplateIndex(),
            compact('page', 'results', 'filter', 'filter_cont', 'count')
        );
    }
}
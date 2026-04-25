<?php

namespace App\Http\Controllers;

use App\Http\Facades\LastModified;
use App\Http\Resources\SeoCustomUrlCatalogResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\ElasticsearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Vis\Builder\TreeController;

class CategoryControllerOld extends TreeController
{
    //Из коробки без кеширования
    /*public function index(Category $page): View|JsonResponse
    {
        $filter = $page->filter()->init();
        $results = (new ElasticsearchService())->filter($page, $filter);
        return $this->getViewResult($page, $filter, $results);
    }*/

    //Добавлено кеширование
    public function index(Category $page): View|JsonResponse
    {
        $filter = $page->filter()->init();
        $cacheKey = 'category_' . $page->getCacheKey() . '_results_' . md5(http_build_query($_GET)) . '_' . md5(serialize($filter));
        $results = Cache::tags(['category', 'elasticsearch'])->remember(
            $cacheKey,
            now()->addDay(),
            function () use ($page, $filter) {
                return (new ElasticsearchService())->filter($page, $filter);
            }
        );
        return $this->getViewResult($page, $filter, $results);
    }

    public function routeCatalog(Category $page): View|JsonResponse
    {
        if (!$page->characteristics()->rememberForever()->cacheTags(['category'])->count()) {
            $filter = $page->filter()->init();
            // Вывод без сортировки и с нулевыми ценами (из коробки)
            //$products = $page->products()->active()->paginate($filter->countShow);
            // Сортировка по нужному полю, без учета статуса (и без нулевых цен)
            //$products = $page->products()->active()->notNullPrice()->sortedBy($filter->getFilterSort())->paginate($filter->getFilterShow());

            // Сортировка по статусу товаров, потом по нужному полю (и без нулевых цен)
            $orderDefault = '`product_status_id` asc';
            //$products = $page->products()->active()->notNullPrice()->orderByRaw($orderDefault)->sortedBy($filter->getFilterSort())->paginate($filter->getFilterShow()); //TODO: без нулевой цены notNullPrice()
            //--

            //TODO: здесь оптимизировать, напр., через ElasticSearch
            $products = Product::leftJoin('category_product', 'category_product.product_id', '=', 'products.id')
                ->where(function ($query) use ($page): void {
                    $query->where('category_product.category_id', '=', $page->id)
                        ->orWhere('products.category_id', '=', $page->id);
                })
                ->active()->available()->notNullPrice()->orderByRaw($orderDefault)->sortedBy($filter->getFilterSort())->paginate($filter->getFilterShow()); //TODO: без нулевой цены notNullPrice()

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
        //Добавлено для избежания двойной отрисовки мобильного и десктопного фильтра.
        //Рендерим фильтр один раз и потом вставляем в шаблоне в двух местах
        /*$filter_cont = view(
            'category.partials.filter_cont',
            compact('page', 'results', 'filter')
        )->render();*/

        //Вариант с кешированием фильтра
        //$count = $results['products']->count();
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
            'category.index',
            compact('page', 'results', 'filter', 'filter_cont', 'count')
        );
    }

    public function rebuildCatagories()
    {
        Category::fixTree();
    }
}

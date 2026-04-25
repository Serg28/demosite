<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeoCustomUrlResource;
use App\Models\Promotion;
use App\Models\Tree;
use App\Services\ElasticsearchPromotionService;
use App\Services\Filters\FilterUniversal;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class PromotionController_ extends TreeController
{

    protected function createFilterPromotion($page): FilterUniversal
    {
        return new FilterUniversal($page);
    }


    /*public function index(): View
    {
        $page = $this->node;
        $promotions = Promotion::filter(request()->get('filter'))->active()->orderPriority()->paginate(4);
        $filters = [
            '' => __t('Все'),
            'new' => __t('Новые'),
            'will_end_soon' => __t('Скоро закончатся')
        ];

        return view('promotion.index', compact('page', 'promotions', 'filters'));
    }*/

    //Рабочий без фильтра
    /*public function index(): View
    {
        $page = $this->node;
        $promotions = Promotion::filterTime(request()->get("filter"))
            ->active()
            ->notFinished()
            ->orderPriority()
            ->paginate(4);

        $filters = [
            "" => __t("Все"),
            "new" => __t("Новые"),
            "will_end_soon" => __t("Скоро закончатся"),
        ];

        // Получаем все товары из всех акций
        $products = Promotion::filterTime(request()->get("filter"))
            ->active()
            ->notFinished()
            ->with([
                "promotionCodeProducts",
                "promotionCodeProducts.characteristicOptions",
            ])
            ->get()
            ->pluck("promotionCodeProducts")
            ->flatten();

        // Получаем уникальные главные категории и бренды
        $categories = collect();
        $brands = collect();

        foreach ($products as $product) {
            $topCategory = $product->getTopCategory();
            if ($topCategory) {
                $categories[$topCategory->getKey()] = $topCategory;
            }

            $brand = $product->brand;
            if ($brand) {
                $brands[$brand->getKey()] = $brand;
            }
        }

        $categories = $categories->values()->unique();
        $brands = $brands->values()->unique();

        return view(
            "promotion.index",
            compact("page", "promotions", "categories", "brands", "filters")
        );
    }*/

    //C фильтром
    public function index(): View|JsonResponse
    {
        $page = $this->node ?? Tree::slug(explode('/', request()->path())[0])->firstOrFail();

        $filters = [
            "" => __t("Все"),
            "new" => __t("Новые"),
            "will_end_soon" => __t("Скоро закончатся"),
        ];

        // Получаем все товары из всех акций
        $products = Promotion::filterTime(request()->get("filter"))
            ->active()
            ->notFinished()
            /*->with([
                "promotionCodeProducts",
                "promotionCodeProducts.characteristicOptions",
            ])*/
            ->get()
            ->pluck("promotionCodeProducts")
            ->flatten();

        $filter = $this->createFilterPromotion($page)->init();

        $filterClass = new ElasticsearchPromotionService();

        $results = $filterClass->filter($page, $filter, $products->pluck("id")->toArray());

        $categories = $products->map(fn ($product) => $product->getTopCategory())->unique();

        $productCodes = $results['products']->pluck('code')->toArray();

        $promotions = Promotion::filterTime(request()->get("filter"))
            ->active()
            ->notFinished()
            ->whereHas('promotionCodeProducts', function ($query) use ($productCodes) {
                $query->whereIn('product_code', $productCodes);
            })
            ->orderPriority()
            ->paginate(4);

        $activeCharacteristics = $filterClass->getCharacteristics($page)->get();

        $categoryFilter = $filter->getFilter()['category'] ?? null;
        $categoryId = !empty($categoryFilter) ? reset($categoryFilter) : null;
        $currentCategory = $categories->firstWhere('id', $categoryId);

        return $this->getViewPromotionsResult($page, $promotions, $categories, $filters, $filter, $results, $activeCharacteristics, $currentCategory);
    }

    /*
    //Рабочий без фильтра
    public function page(Promotion $promotion): View
    {
        $page = $promotion;
        $products = $page->promotionCodeProducts()->active()->with('category')->paginate(20);

        $topCategories = collect();
        $brands = collect();

        $allProducts = $page->promotionCodeProducts()->active()->with('category')->get();
        foreach ($allProducts as $product) {
            $topCategory = $product->getTopCategory();
            if ($topCategory) {
                $topCategories->push($topCategory);
            }

            $brand = $product->brand;
            if ($brand) {
                $brands[$brand->getKey()] = $brand;
            }
        }

        $categories = $topCategories->unique();

        return view('promotion.page', compact('page', 'products', 'brands', 'categories'));
    }
    */

    //C фильтром
    /*public function page(
        Promotion $promotion,
        //ElasticSearch $elasticsearch
    ): View|JsonResponse {
        $page = $promotion;

        $filter = $page->filter()->init();

        $activeProducts = $page->promotionCodeProducts()->active();
        $allProducts = $activeProducts->with("category")->get();

        $results = (new ElasticsearchPromotionService())->filter($page, $filter, $activeProducts->pluck("id")->toArray());

        //$result = $elasticsearch
          //  ->searchPromotion(
          //      $activeProducts->getQuery(),
          //      $activeProducts->pluck("id")->toArray(),
          //      $filter
          //  );


        $topCategories = collect();
        $brands = collect();

        foreach ($allProducts as $product) {
            $topCategory = $product->getTopCategory();
            if ($topCategory) {
                $topCategories->push($topCategory);
            }

            $brand = $product->brand;
            if ($brand) {
                $brands[$brand->getKey()] = $brand;
            }
        }

        $categories = $topCategories->unique();

        return $this->getViewResult($page, $filter, $results, $brands, $categories);
        //return view(
        //    "promotion.page",
        //    compact("page", "products", "brands", "categories", "filter")
        //);
    }*/

    public function page(Promotion $promotion): View|JsonResponse
    {
        $filter = $promotion->filter()->init();
        $activeProducts = $promotion->promotionCodeProducts()->active()->get();

        $cacheKey = 'promotion_filter_' . $promotion->getCacheKey() . '_results_' . md5(http_build_query($_GET)) . '_' . md5(serialize($filter) . serialize($activeProducts));
        $results = Cache::tags(['promotion_filter', 'promotion', 'category', 'elasticsearch'])->remember(
            $cacheKey,
            now()->addDay(),
            fn () => (new ElasticsearchPromotionService())->filter($promotion, $filter, $activeProducts->pluck("id")->toArray())
        );

        $categories = $activeProducts->map(fn ($product) => $product->getTopCategory())->unique();
        $categoryFilter = $filter->getFilter()['category'] ?? null;
        $categoryId = !empty($categoryFilter) ? reset($categoryFilter) : null;
        $currentCategory = $categories->firstWhere('id', $categoryId);

        return $this->getViewProductsResult($promotion, $filter, $results, $categories, $currentCategory);
    }





    private function getViewProductsResult(Promotion $page, $filter, $results, $categories, $currentCategory): View|JsonResponse
    {
        $count = $results['products']->total();
        $count = $count ?: 0;
        $products = $results['products'];

        $cacheKey = 'promotion_products_filter_' . $page->getCacheKey() . '_' . md5(serialize($filter) . serialize($results));
        $filter_block = Cache::tags(['promotion_filter', 'products', 'categories', 'characteristics'])
            ->remember($cacheKey, now()->addWeek(), function () use ($page, $filter, $results, $count, $categories, $currentCategory) {
                return view(
                    'promotion.partials.categories',
                    compact("page", "categories", "filter", 'count', 'results', 'currentCategory')
                )->render();
            });

        if (request()->ajax()) {

            if (request()->has('show-more')) {
                return response()->json([
                    'products' => view('promotion.partials.products', compact('filter', 'results', 'products'))->render(),
                    'links' => view('partials.paginate', compact('filter', 'results', 'products'))->render(),
                ]);
            }

            return response()->json([
                'html' => view(
                    'promotion.partials.center',
                    compact('page', 'results', 'filter', 'count', 'products', 'categories', "filter_block")
                )->render(),
                'seo' => new SeoCustomUrlResource($page),
                'count' => $count
            ]);
        }

        return view(
            'promotion.page_orig',
            compact("page", "products", "categories", 'currentCategory', "filter", "filter_block", "count")
        );
    }

    private function getViewPromotionsResult(Tree $page, $promotions, $categories, $filters, $filter, $results, $activeCharacteristics, $currentCategory): View|JsonResponse
    {
        $count = $promotions->total();
        $count = $count ?: 0;
        $products = $results['products'];

        $cacheKey = 'promotion_promotion_filter_' . $page->getCacheKey() . '_' . md5(serialize($filter) . serialize($results));
        $filter_block = Cache::tags(['promotion_filter', 'products', 'categories', 'characteristics'])
            ->remember($cacheKey, now()->addWeek(), function () use ($page, $categories, $filter, $results, $count, $activeCharacteristics, $currentCategory) {
                return view(
                    'promotion.partials.categories_index',
                    compact("page", "categories", "filter", "results", "count", "activeCharacteristics", "currentCategory")
                )->render();
            });

        if (request()->ajax()) {

            if (request()->has('show-more')) {
                return response()->json([
                    'products' => view('promotion.partials.promotions', compact('filter', 'results', 'promotions'))->render(),
                    'links' => view('partials.paginate', compact('filter', 'results', 'products'))->render(),
                ]);
            }

            return response()->json([
                'html' => view(
                    'promotion.partials.center_index',
                    compact('page', "promotions", 'results', 'filter', 'count', 'products', 'categories', "filter_block")
                )->render(),
                'seo' => new SeoCustomUrlResource($page),
                'count' => $count
            ]);
        }

        return view(
            "promotion.index",
            compact("page", "promotions", "categories", "filters", "filter", "results", "filter_block", "count", "currentCategory")
        );
    }
}

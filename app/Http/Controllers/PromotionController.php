<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeoCustomUrlResource;
use App\Models\Promotion;
use App\Models\Tree;
use App\Services\Promotion as PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class PromotionController extends TreeController
{
    protected PromotionService $promotionService;

    /**
     * Создание экземпляра контроллера PromotionController.
     *
     * @param PromotionService $promotionService
     */
    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Отображает страницу акции и её фильтрацию.
     *
     * @param Promotion $promotion
     * @return View|JsonResponse
     */
    public function page(Promotion $promotion)
    {
        $filter = $promotion->filter()->init();

        $data = $this->promotionService->getPromotionData($promotion, $filter);

        return $this->getViewProductsResult($promotion, $filter, $data['results'], $data['categories'], $data['currentCategory']);
    }

    /**
     * Получение данных о всех акциях и их фильтрации.
     *
     * @return JsonResponse|View
     */
    public function index(): JsonResponse|View
    {
        $page = $this->node ?? Tree::slug($this->promotionService->getPageSlug())->firstOrFail();
        $filter = $this->promotionService->createFilter($page)->init();

        $filters = [
            "" => __t("Все"),
            "new" => __t("Новые"),
            "will_end_soon" => __t("Скоро закончатся"),
        ];

        //Временно выводим обычный шаблон
        return view('promotion.page', compact('page', 'filters', 'filter'));

        // TODO: доработать.
        /*
        $data = $this->promotionService->getAllPromotionsData($page, $filter);

        return $this->getViewPromotionsResult(
            $page,
            $data['promotions'],
            $data['categories'],
            $filters,
            $filter,
            $data['results'],
            $data['activeCharacteristics'],
            $data['currentCategory']
        );*/
    }

    private function getViewProductsResult(Promotion $page, $filter, $results, $categories, $currentCategory): View|JsonResponse
    {
        $products = $results['products'] ?? null;
        $count = $products ? $products->count() : 0;

        $cacheKey = 'promotion_products_filter_' . $page->getCacheKey() . '_' . md5(serialize($filter) . serialize($results)).md5(http_build_query(request()->all()));
        $filter_block = Cache::tags(['promotion_filter', 'products', 'categories', 'characteristics'])
            ->remember($cacheKey, now()->addWeek(), function () use ($page, $filter, $results, $count, $categories, $currentCategory) {
                return view('promotion.partials.categories',
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
        $count = $promotions->total() ?? 0;
        $products = $results['products'];

        $cacheKey = 'promotion_promotion_filter_' . $page->getCacheKey() . '_' . md5(serialize($filter) . serialize($results)) . md5(http_build_query(request()->all()));
        $filter_block = Cache::tags(['promotion_filter', 'products', 'categories', 'characteristics'])
            ->remember($cacheKey, now()->addWeek(), function () use ($page, $categories, $filter, $results, $count, $activeCharacteristics, $currentCategory) {
                return view('promotion.partials.categories_index',
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

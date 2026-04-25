<?php

namespace App\Services;

use App\Models\Promotion as PromotionModel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use App\Services\Filters\FilterUniversal;

class Promotion
{
    protected ElasticsearchPromotionService $elasticsearchPromotionService;

    /**
     * Создание экземпляра сервиса Promotion.
     *
     * @param ElasticsearchPromotionService $elasticsearchPromotionService
     */
    public function __construct(ElasticsearchPromotionService $elasticsearchPromotionService)
    {
        $this->elasticsearchPromotionService = $elasticsearchPromotionService;
    }

    /**
     * Создает новый экземпляр фильтра типа FilterUniversal для указанной страницы акций.
     *
     * @param mixed $page Страница акций, для которой создается фильтр.
     * @return FilterUniversal Новый экземпляр фильтра типа FilterUniversal.
     */
    public function createFilter($page): FilterUniversal
    {
        return new FilterUniversal($page);
    }

    /**
     * Получение данных о всех акциях и их фильтрации.
     *
     * @param mixed $page
     * @param FilterUniversal $filter
     * @return array
     */
    public function getAllPromotionsData($page, FilterUniversal $filter): array
    {
        $products = $this->getActiveNotFinishedProducts();
        //$filter->setFilterShow(1000);
        $cacheKey = 'promotion_filter_results_' . md5(http_build_query(request()->all())) . '_' . md5(serialize($filter));
        $results = $this->getCachedResults($cacheKey, function () use ($page, $filter, $products) {
            return $this->elasticsearchPromotionService->filter($page, $filter, $products->pluck("id")->toArray(), true);
        });

        $categories = $this->getUniqueTopCategories($products);
        $promotions = $this->getFilteredPromotions($products, $results);
        $activeCharacteristics = $this->elasticsearchPromotionService->getCharacteristics($page)->get();
        $currentCategory = $this->getCurrentCategory($filter, $categories);

        return compact('promotions', 'categories', 'results', 'activeCharacteristics', 'currentCategory');
    }

    /**
     * Получение данных о конкретной акции и её фильтрации.
     *
     * @param PromotionModel $promotion
     * @param mixed $filter
     * @return array
     */
    public function getPromotionData(PromotionModel $promotion, $filter): array
    {
        $activeProducts = $promotion->promotionCodeProducts()->active()->get();

        $cacheKey = $this->generateCacheKey($promotion, $filter, $activeProducts);
        $results = $this->getCachedResults($cacheKey, function () use ($promotion, $filter, $activeProducts) {
            return $this->elasticsearchPromotionService->filter($promotion, $filter, $activeProducts->pluck("id")->toArray());
        });

        $categories = $this->getUniqueTopCategories($activeProducts);
        $currentCategory = $this->getCurrentCategory($filter, $categories);

        return compact('results', 'categories', 'currentCategory');
    }

    /*
     * Получение списка активных не завершенных продуктов.
     *
     * @return mixed
     */
    private function getActiveNotFinishedProducts()
    {
        return PromotionModel::filterTime(request()->get("filter"))
            ->with('promotionCodeProducts')
            ->active()
            ->notFinished()
            ->get()
            ->pluck("promotionCodeProducts")
            ->flatten();
    }

    /**
     * Получение уникальных главных категорий продуктов.
     *
     * @param mixed $products
     * @return mixed
     */
    private function getUniqueTopCategories($products)
    {
        return $products->map(fn ($product) => $product->getTopCategory())->unique();
    }

    /*
     * Фильтрация акций на основе данных из Elasticsearch.
     *
     * @param mixed $products
     * @param mixed $results
     * @return mixed
     */
    private function getFilteredPromotions($products, $results): mixed
    {
        $productCodes = $results['products']->pluck('code')->toArray();

        return PromotionModel::filterTime(request()->get("filter"))
            ->active()
            ->notFinished()
            ->whereHas('promotionCodeProducts', function ($query) use ($productCodes) {
                $query->whereIn('product_code', $productCodes);
            })
            ->orderPriority()
            ->paginate(4);
    }

    /**
     * Получение текущей категории из фильтра.
     *
     * @param mixed $filter
     * @param mixed $categories
     * @return mixed|null
     */
    private function getCurrentCategory($filter, $categories): mixed
    {
        $categoryFilter = $filter->getFilter()['category'] ?? null;
        $categoryId = !empty($categoryFilter) ? reset($categoryFilter) : null;
        return $categories->firstWhere('id', $categoryId);
    }

    /**
     * Генерация ключа кэша на основе акции, фильтра и активных продуктов.
     *
     * @param PromotionModel $promotion
     * @param mixed $filter
     * @param mixed $activeProducts
     * @return string
     */
    private function generateCacheKey(PromotionModel $promotion, $filter, $activeProducts): string
    {
        return 'promotion_filter_' . $promotion->getCacheKey() . '_results_' . md5(http_build_query($_GET)) . '_' . md5(serialize($filter) . serialize($activeProducts));
    }

    /**
     * Универсальный метод получения кэшированных результатов из Elasticsearch.
     *
     * @param string $cacheKey
     * @param callable $searchFunction
     * @return mixed
     */
    private function getCachedResults(string $cacheKey, callable $searchFunction): mixed
    {
        return Cache::tags(['promotion_filter', 'promotion', 'category', 'elasticsearch'])->remember(
            $cacheKey,
            now()->addDay(),
            $searchFunction
        );
    }

    /**
     * Получает slug страницы из URL.
     *
     * @return string Slug страницы из URL.
     */
    public function getPageSlug(): string
    {
        $url = request()->path();
        $locale = App::getLocale();

        return explode('/', str_replace($locale.'/', '', $url))[0];
    }

}

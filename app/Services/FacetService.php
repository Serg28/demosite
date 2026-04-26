<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * FacetService — управление фасетами каталога с многоуровневым кэшем.
 *
 * Архитектура:
 * - Фасеты кэшируются отдельно от товаров (разные TTL)
 * - Поддержка expanded фасетов (show more)
 * - Precise cache tags для инвалидации
 */
class FacetService
{
    private const FACET_CACHE_TTL = 15; // минут

    private const EXPANDED_FACET_CACHE_TTL = 60; // минут

    private const FACET_PAGE_SIZE = 15; // Первоначально показываем 15

    private const EXPANDED_PAGE_SIZE = 50; // При развертывании показываем по 50

    /**
     * Получить все фасеты для категории.
     *
     * @param  array  $expandedFacets  ID характеристик, развернутых пользователем
     * @param  array  $currentFilters  Текущие выбранные фильтры (для подсчета)
     */
    public function getFacetsForCategory(
        Category $category,
        array $expandedFacets = [],
        array $currentFilters = []
    ): array {
        $cacheKey = $this->generateFacetCacheKey($category->id, $currentFilters);

        return Cache::tags(['category_'.$category->id, 'facets'])
            ->remember($cacheKey, now()->addMinutes(self::FACET_CACHE_TTL), function () use ($category, $expandedFacets, $currentFilters) {
                return [
                    'price' => $this->getPriceFacet($category, $currentFilters),
                    'characteristics' => $this->getCharacteristicsFacets($category, self::FACET_PAGE_SIZE, $expandedFacets, $currentFilters),
                ];
            });
    }

    /**
     * Получить развернутый фасет (для "Показати ще").
     */
    public function getExpandedFacet(
        Category $category,
        int $characteristicId,
        int $page = 1,
        int $pageSize = self::EXPANDED_PAGE_SIZE,
        array $currentFilters = []
    ): array {
        $cacheKey = "facet_expanded:{$category->id}:{$characteristicId}:{$page}";

        return Cache::tags(['category_'.$category->id, 'facet_expanded'])
            ->remember($cacheKey, now()->addMinutes(self::EXPANDED_FACET_CACHE_TTL), function () use ($category, $characteristicId, $page, $pageSize, $currentFilters) {
                $characteristic = Characteristic::findOrFail($characteristicId);

                $skip = ($page - 1) * $pageSize;
                $options = CharacteristicOption::where('characteristic_id', $characteristicId)
                    ->skip($skip)
                    ->limit($pageSize + 1) // +1 для проверки has_more
                    ->get();

                $hasMore = $options->count() > $pageSize;
                $items = $options->take($pageSize);

                return [
                    'characteristic_id' => $characteristicId,
                    'characteristic_title' => $characteristic->title['ua'] ?? $characteristic->title['ru'] ?? '',
                    'options' => $items->map(fn ($opt) => [
                        'id' => $opt->id,
                        'title' => $opt->title['ua'] ?? $opt->title['ru'] ?? '',
                        'slug' => $opt->slug,
                        'count' => $this->getOptionCountInCategory($category->id, $characteristicId, $opt->id, $currentFilters),
                    ])->values()->toArray(),
                    'page' => $page,
                    'per_page' => $pageSize,
                    'has_more' => $hasMore,
                ];
            });
    }

    /**
     * Получить фасет цены.
     */
    private function getPriceFacet(Category $category, array $currentFilters): array
    {
        $cacheKey = "facet_price:{$category->id}";

        return Cache::tags(['category_'.$category->id, 'facet_price'])
            ->remember($cacheKey, now()->addHours(1), function () use ($category) {
                // Получаем min/max цену в этой категории
                $priceStats = \DB::table('products')
                    ->where('category_id', $category->id)
                    ->where('is_active', true)
                    ->select(
                        \DB::raw('MIN(price) as min_price'),
                        \DB::raw('MAX(price) as max_price')
                    )
                    ->first();

                return [
                    'min' => (int) ($priceStats->min_price ?? 0),
                    'max' => (int) ($priceStats->max_price ?? 0),
                ];
            });
    }

    /**
     * Получить фасеты характеристик.
     */
    private function getCharacteristicsFacets(
        Category $category,
        int $limit,
        array $expandedFacets,
        array $currentFilters
    ): array {
        // Получить активные характеристики для этой категории
        $characteristics = $category->characteristics()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        return $characteristics->map(function ($char) use ($category, $limit, $expandedFacets, $currentFilters) {
            $cacheKey = "facet_char:{$category->id}:{$char->id}";
            $pageSize = in_array($char->id, $expandedFacets) ? 100 : $limit;

            return Cache::tags(['category_'.$category->id, "facet_char_{$char->id}"])
                ->remember($cacheKey, now()->addMinutes(self::FACET_CACHE_TTL), function () use ($category, $char, $pageSize, $currentFilters) {
                    $options = $char->options()
                        ->limit($pageSize + 1) // +1 для has_more
                        ->get();

                    $hasMore = $options->count() > $pageSize;
                    $items = $options->take($pageSize);

                    return [
                        'facet_id' => 'characteristic_'.$char->id,
                        'characteristic_id' => $char->id,
                        'characteristic_title' => $char->title['ua'] ?? $char->title['ru'] ?? '',
                        'is_range_type' => $char->is_range_type ?? false,
                        'options' => $items->map(fn ($opt) => [
                            'id' => $opt->id,
                            'title' => $opt->title['ua'] ?? $opt->title['ru'] ?? '',
                            'slug' => $opt->slug,
                            'count' => $this->getOptionCountInCategory($category->id, $char->id, $opt->id, $currentFilters),
                        ])->toArray(),
                        'has_more' => $hasMore,
                    ];
                });
        })->toArray();
    }

    /**
     * Получить количество товаров с конкретной опцией в категории.
     * Кэшируется отдельно для быстрого доступа.
     */
    private function getOptionCountInCategory(
        int $categoryId,
        int $characteristicId,
        int $optionId,
        array $currentFilters
    ): int {
        $cacheKey = "option_count:{$categoryId}:{$characteristicId}:{$optionId}";

        return Cache::tags(['category_'.$categoryId, "option_{$optionId}"])
            ->remember($cacheKey, now()->addHours(1), function () use ($categoryId, $optionId) {
                return Product::query()
                    ->where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->whereIn('id', function ($q) use ($optionId) {
                        $q->select('product_id')
                            ->from('product_characteristic_options')
                            ->where('characteristic_option_id', $optionId);
                    })
                    ->count();
            });
    }

    /**
     * Инвалидировать кэш фасетов для категории.
     */
    public function invalidateCategoryFacets(Category $category): void
    {
        Cache::tags(['category_'.$category->id, 'facets', 'facet_expanded'])->flush();
    }

    /**
     * Инвалидировать цены.
     */
    public function invalidatePrices(Category $category): void
    {
        Cache::tags(['category_'.$category->id, 'facet_price'])->flush();
    }

    /**
     * Генерировать ключ кэша для фасетов.
     */
    private function generateFacetCacheKey(int $categoryId, array $filters): string
    {
        $hash = md5(json_encode($filters));

        return "facets:{$categoryId}:{$hash}";
    }
}

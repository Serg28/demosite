<?php

namespace App\Services;

use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use Illuminate\Support\Facades\Cache;

class Category_orig
{
    protected ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function hasChildren(CategoryModel $page): bool
    {
        return $page->children()->rememberForever()->cacheTags(['categories'])->exists();
    }

    public function getElasticsearchData(CategoryModel $page): array
    {
        $filter = $page->filter()->init();
        $cacheKey = 'category_' . $page->getCacheKey() . '_results_' . md5(http_build_query($_GET)) . '_' . md5(serialize($filter));
        $results = Cache::tags(['category', 'elasticsearch'])->remember(
            $cacheKey,
            now()->addDay(),
            function () use ($page, $filter) {
                return $this->elasticsearchService->filter($page, $filter);
            }
        );
        return [
            'page' => $page,
            'filter' => $filter,
            'results' => $results
        ];
    }

    public function routeCatalog(CategoryModel $page): ?array
    {
        if (!$page->characteristics()->rememberForever()->cacheTags(['category'])->exists()) {
            $filter = $page->filter()->init();

            $products = $this->getProductsForCategory($page, $filter);

            return [
                'page' => $page,
                'products' => $products,
                'filter' => $filter,
            ];
        }
        return null;
    }

    // Метод для получения продуктов категории
    private function getProductsForCategory(CategoryModel $page, $filter)
    {
        $orderDefault = '`product_status_id` asc';

        return ProductModel::inCategories($page)
            ->active()->available()->notNullPrice()->orderByRaw($orderDefault)->sortedBy($filter->getFilterSort())->paginate($filter->getFilterShow());
    }

    // Метод для получения данных о подкатегориях
    public function getRubricsForCategory(CategoryModel $page)
    {
        return $page->children()->active()->defaultOrder()->get();
    }

}


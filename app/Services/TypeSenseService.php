<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Builder;

/**
 * TypeSenseService — основной сервис поиска товаров для 1M+ каталогов.
 *
 * Архитектура разделяет ответственность:
 * - Поиск товаров (быстро: 50-200ms)
 * - Получение фасетов (из Redis: 1-5ms)
 * - Поиск среди опций (отдельный сервис)
 */
class TypeSenseService
{
    private string $collectionName = 'products';
    private int $defaultPageSize = 18;

    public function __construct()
    {
        // TypeSense может быть настроен через Scout конфиг
    }

    /**
     * Поиск товаров с фильтрацией.
     *
     * @param string $query Поисковый запрос (может быть пустым)
     * @param array $filters Фильтры: ['price' => ['min' => 100, 'max' => 500], 'brands' => [1, 2, 3]]
     * @param int $page Номер страницы
     * @param int $pageSize Товаров на страницу
     * @param string $sortBy Поле для сортировки
     * @param string $sortDir Направление: asc|desc
     * @return array ['products' => [], 'total' => int, 'pagination' => [...]]
     */
    public function search(
        string $query = '',
        array $filters = [],
        int $page = 1,
        int $pageSize = 18,
        string $sortBy = 'relevance',
        string $sortDir = 'desc'
    ): array {
        $pageSize = min($pageSize, 100); // Максимум 100 на страницу

        try {
            // Строим базовый запрос
            $builder = $this->buildQuery($query, $filters, $sortBy, $sortDir);

            // Выполняем поиск с пагинацией
            $results = $builder->paginate($pageSize, 'page', $page);

            return [
                'products' => $results->items(),
                'total' => $results->total(),
                'per_page' => $pageSize,
                'current_page' => $page,
                'last_page' => $results->lastPage(),
                'has_more' => $page < $results->lastPage(),
            ];
        } catch (\Exception $e) {
            \Log::error('TypeSense search error: ' . $e->getMessage());
            return [
                'products' => [],
                'total' => 0,
                'per_page' => $pageSize,
                'current_page' => $page,
                'last_page' => 1,
                'has_more' => false,
            ];
        }
    }

    /**
     * Получить общее количество товаров по фильтрам.
     */
    public function count(string $query = '', array $filters = []): int
    {
        try {
            return $this->buildQuery($query, $filters)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Строит базовый Scout Builder с фильтрами.
     */
    private function buildQuery(
        string $query = '',
        array $filters = [],
        string $sortBy = 'relevance',
        string $sortDir = 'desc'
    ): Builder {
        $builder = Product::query();

        // Текстовый поиск
        if (!empty($query)) {
            $builder = $builder->search($query);
        }

        // Фильтры по цене
        if (!empty($filters['price'])) {
            $minPrice = $filters['price']['min'] ?? null;
            $maxPrice = $filters['price']['max'] ?? null;

            if ($minPrice !== null) {
                $builder->where('price', '>=', $minPrice);
            }
            if ($maxPrice !== null) {
                $builder->where('price', '<=', $maxPrice);
            }
        }

        // Фильтры по бренду
        if (!empty($filters['brands']) && is_array($filters['brands'])) {
            $builder->whereIn('brand_id', $filters['brands']);
        }

        // Фильтры по категории
        if (!empty($filters['category_id'])) {
            $builder->where('category_id', $filters['category_id']);
        }

        // Фильтры по характеристикам (пока без range filters)
        // TODO: Реализовать при необходимости через характеристики

        // Сортировка
        $this->applySorting($builder, $sortBy, $sortDir);

        // Исключаем неактивные товары
        $builder->where('is_active', true);

        return $builder;
    }

    /**
     * Применяет сортировку к запросу.
     */
    private function applySorting(Builder $builder, string $sortBy, string $sortDir): void
    {
        $sortDir = strtoupper($sortDir) === 'DESC' ? 'desc' : 'asc';

        match ($sortBy) {
            'price' => $builder->orderBy('price', $sortDir),
            'rating' => $builder->orderBy('rating', $sortDir),
            'newest' => $builder->orderBy('created_at', 'desc'),
            'popular' => $builder->orderBy('priority', 'desc'),
            default => $builder->orderBy('priority', 'desc'),
        };
    }

    /**
     * Получить базовую информацию о товаре для карточки.
     */
    public function getProductCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'title' => $product->title['ua'] ?? $product->title['ru'] ?? '',
            'price' => $product->price,
            'price_old' => $product->price_old,
            'slug' => $product->slug,
            'is_active' => $product->is_active,
            'priority' => $product->priority,
        ];
    }

    /**
     * Переиндексировать все товары в TypeSense.
     * Запускается через artisan команду.
     */
    public function indexAllProducts(): void
    {
        Product::truncateIndex();
        Product::makeAllSearchable();
    }

    /**
     * Обновить товар в индексе.
     */
    public function updateProductIndex(Product $product): void
    {
        $product->searchable();
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Builder as ScoutBuilder;

class TypeSenseService
{
    /**
     * @param  array{category_id?: int, min_price?: float, max_price?: float, characteristics?: array<int, int[]>}  $filters
     * @return array{products: mixed[], total: int, per_page: int, current_page: int, last_page: int, has_more: bool}
     */
    public function search(
        string $query = '',
        array $filters = [],
        int $page = 1,
        int $pageSize = 18,
        string $sortBy = 'relevance',
        string $sortDir = 'desc'
    ): array {
        $pageSize = min($pageSize, 100);

        try {
            $paginator = $this->buildScoutQuery($query, $filters, $sortBy, $sortDir)
                ->paginate($pageSize, 'page', $page);

            return [
                'products' => $paginator->items(),
                'total' => $paginator->total(),
                'per_page' => $pageSize,
                'current_page' => $page,
                'last_page' => $paginator->lastPage(),
                'has_more' => $page < $paginator->lastPage(),
            ];
        } catch (\Exception $e) {
            Log::error('TypeSense search error', ['message' => $e->getMessage(), 'filters' => $filters]);

            return $this->eloquentFallback($filters, $page, $pageSize);
        }
    }

    public function count(string $query = '', array $filters = []): int
    {
        try {
            return $this->buildScoutQuery($query, $filters)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function indexAllProducts(): void
    {
        Product::makeAllSearchable();
    }

    public function updateProductIndex(Product $product): void
    {
        $product->searchable();
    }

    private function buildScoutQuery(
        string $query = '',
        array $filters = [],
        string $sortBy = 'relevance',
        string $sortDir = 'desc'
    ): ScoutBuilder {
        $builder = Product::search($query ?: '*');

        $builder->where('is_active', true);

        if (! empty($filters['category_id'])) {
            $builder->where('category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['min_price'])) {
            $builder->where('price', '>=', (float) $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $builder->where('price', '<=', (float) $filters['max_price']);
        }

        // Characteristic filters: AND between groups, OR within same group
        $characteristics = array_filter($filters['characteristics'] ?? [], fn ($ids) => ! empty($ids));
        if (! empty($characteristics)) {
            $productIds = $this->getProductIdsByCharacteristics($characteristics);

            if ($productIds->isNotEmpty()) {
                $builder->whereIn('id', $productIds->map(fn ($id) => (string) $id)->toArray());
            } else {
                // No products match — force empty result
                $builder->where('id', -1);
            }
        }

        $this->applyScoutSorting($builder, $sortBy, $sortDir);

        return $builder;
    }

    /**
     * AND between groups, AND within each group.
     * Each selected option requires a separate subquery — the product must have ALL options.
     */
    private function getProductIdsByCharacteristics(array $characteristics): Collection
    {
        $query = Product::query()->select('id');

        foreach ($characteristics as $groupValue) {
            // Range-type group — skip (handled separately via price-like filters in the future)
            if (is_array($groupValue) && isset($groupValue['type'])) {
                continue;
            }

            $optionIds = array_values(array_filter((array) $groupValue));
            if (empty($optionIds)) {
                continue;
            }

            // AND within group: each option is its own subquery
            foreach ($optionIds as $optionId) {
                $query->whereIn('id', function ($q) use ($optionId) {
                    $q->select('product_id')
                        ->from('product_characteristic_options')
                        ->where('characteristic_option_id', (int) $optionId);
                });
            }
        }

        return $query->pluck('id');
    }

    private function applyScoutSorting(ScoutBuilder $builder, string $sortBy, string $sortDir): void
    {
        $dir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';

        match ($sortBy) {
            'price' => $builder->orderBy('price', $dir),
            'newest' => $builder->orderBy('created_at', 'desc'),
            'popular' => $builder->orderBy('priority', 'desc'),
            default => $builder->orderBy('priority', 'desc'),
        };
    }

    /**
     * @return array{products: mixed[], total: int, per_page: int, current_page: int, last_page: int, has_more: bool}
     */
    private function eloquentFallback(array $filters, int $page, int $pageSize): array
    {
        $query = Product::query()->where('is_active', true);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $characteristics = array_filter($filters['characteristics'] ?? [], fn ($ids) => ! empty($ids));
        if (! empty($characteristics)) {
            $productIds = $this->getProductIdsByCharacteristics($characteristics);
            $query->whereIn('id', $productIds);
        }

        $query->orderBy('priority', 'desc');

        $paginator = $query->paginate($pageSize, ['*'], 'page', $page);

        return [
            'products' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $pageSize,
            'current_page' => $page,
            'last_page' => $paginator->lastPage(),
            'has_more' => $page < $paginator->lastPage(),
        ];
    }
}

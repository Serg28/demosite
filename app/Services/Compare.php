<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Compare
{
    private $compare = [];

    public function __construct()
    {
        $this->compare['compare'] = session('compare');
    }

    public function add(Product $product): void
    {
        $this->compare['compare'][$product->category_id][$product->id] = $product->id;

        session($this->compare);
    }

    public function remove(Product $product): void
    {
        unset($this->compare['compare'][$product->category_id][$product->id]);
        if (empty($this->compare['compare'][$product->category_id])) {
            unset($this->compare['compare'][$product->category_id]);
        }
        session($this->compare);
    }

    public function count(): int
    {
        return (array_sum(Arr::flatten($this->compare)) > 0) ? count(Arr::flatten($this->compare)) : 0;
        //return count(Arr::flatten($this->compare));
    }

    public function check(Product $product): bool
    {
        return isset($this->compare['compare'][$product->category_id][$product->id]);
    }

    public function isCategoryExists(?string $category): bool
    {
        return isset($this->compare['compare'][$category]);
    }

    public function getProductsByIds(array $arrayIdsProduct): Collection
    {
        return Product::whereIn('id', $arrayIdsProduct)->get();
    }

    public function getCompareSession()
    {
        return $this->compare['compare'];
    }

    public function getExistsCategories()
    {
        return Category::find(array_keys($this->compare['compare']));
    }

    /**
     * Возвращает количество товаров в каждой категории
     */
    public function countByCategory(): array
    {
        $counts = [];
        if(!empty($this->compare) && isset($this->compare['compare'])){
            foreach ($this->compare['compare'] as $categoryId => $products) {
                $counts[$categoryId] = count($products);
            }
        }
        return $counts; // Массив с ключами (ID категорий) и значениями (количество товаров)
    }
}

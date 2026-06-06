<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Сервіс порівняння товарів (session-based, доступний анонімним).
 */
class CompareService
{
    private const SESSION_KEY = 'compare';
    private const MAX_ITEMS = 4;

    /**
     * Додати товар до порівняння.
     * Повертає false якщо вже досягнуто ліміт.
     */
    public function add(Product $product): bool
    {
        $ids = $this->productIds();

        if (count($ids) >= self::MAX_ITEMS) {
            return false;
        }

        if (! in_array($product->id, $ids, true)) {
            $ids[] = $product->id;
            Session::put(self::SESSION_KEY, $ids);
        }

        return true;
    }

    /**
     * Видалити товар з порівняння.
     */
    public function remove(Product $product): void
    {
        $ids = array_filter($this->productIds(), fn (int $id): bool => $id !== $product->id);
        Session::put(self::SESSION_KEY, array_values($ids));
    }

    /**
     * Перемкнути стан товару в порівнянні.
     * Повертає true = додано, false = видалено або ліміт досягнуто.
     */
    public function toggle(Product $product): bool
    {
        if ($this->check($product)) {
            $this->remove($product);

            return false;
        }

        return $this->add($product);
    }

    /**
     * Кількість товарів у порівнянні.
     */
    public function count(): int
    {
        return count($this->productIds());
    }

    /**
     * Перевірити чи товар у порівнянні.
     */
    public function check(Product $product): bool
    {
        return in_array($product->id, $this->productIds(), true);
    }

    /**
     * Масив ID товарів у порівнянні.
     *
     * @return int[]
     */
    public function productIds(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Колекція товарів у порівнянні.
     */
    public function products(): Collection
    {
        $ids = $this->productIds();

        if (empty($ids)) {
            return new Collection;
        }

        return Product::query()->whereIn('id', $ids)->get();
    }

    /**
     * Очистити список порівняння.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}

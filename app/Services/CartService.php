<?php

namespace App\Services;

use App\Cart\CartContext;
use App\Models\Product;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Linecore\Shoppingcart\CartItem;
use Linecore\Shoppingcart\Facades\Cart;

class CartService
{
    public function __construct(private readonly string $instance = 'default') {}

    /** Фабричний метод для конкретного instance */
    public static function for(string $instance): static
    {
        return new static($instance);
    }

    public function getInstance(): string
    {
        return $this->instance;
    }

    // ─── Proxy методи Cart ───────────────────────────────────────────

    public function add(
        int|string $id,
        string $name,
        int $qty,
        float $price,
        float $weight = 0,
        array $options = []
    ): CartItem {
        return Cart::instance($this->instance)->add($id, $name, $qty, $price, $weight, $options);
    }

    public function update(string $rowId, mixed $qty): CartItem
    {
        return Cart::instance($this->instance)->update($rowId, $qty);
    }

    public function remove(string $rowId): void
    {
        Cart::instance($this->instance)->remove($rowId);
    }

    public function get(string $rowId): CartItem
    {
        return Cart::instance($this->instance)->get($rowId);
    }

    public function content(): Collection
    {
        return Cart::instance($this->instance)->content();
    }

    public function count(): int
    {
        return Cart::instance($this->instance)->count();
    }

    public function total(int $decimals = 2, string $decimalPoint = '.', string $thousandSep = ''): string
    {
        return Cart::instance($this->instance)->total($decimals, $decimalPoint, $thousandSep);
    }

    public function subtotal(int $decimals = 2, string $decimalPoint = '.', string $thousandSep = ''): string
    {
        return Cart::instance($this->instance)->subtotal($decimals, $decimalPoint, $thousandSep);
    }

    public function weightFloat(): float
    {
        return Cart::instance($this->instance)->weightFloat();
    }

    public function destroy(): void
    {
        Cart::instance($this->instance)->destroy();
    }

    public function search(callable $search): Collection
    {
        return Cart::instance($this->instance)->search($search);
    }

    // ─── Бізнес-логіка ──────────────────────────────────────────────

    /**
     * Кількість одиниць конкретного товару в корзині.
     */
    public function countItem(Product $product): int
    {
        return (int) $this->findItem($product)->sum('qty');
    }

    /**
     * Знайти рядки кошика для конкретного товару.
     */
    public function findItem(Product $product): Collection
    {
        return $this->search(fn ($item) => $item->id == $product->id);
    }

    /**
     * Масив product_id всіх товарів у кошику.
     *
     * @return int[]
     */
    public function getIdsProductsInCart(): array
    {
        return $this->content()->pluck('id')->unique()->values()->all();
    }

    /**
     * Кількість доступного для замовлення товару з урахуванням вже доданого до кошика.
     *
     * @param string $action 'add'|'update'
     */
    public function availableForOrder(Product $product, int $qty, string $action = 'add'): int
    {
        $stock = $product->getQuantity();

        if ($action === 'update') {
            // При зміні кількості — просто обмежуємо стоком
            return min(max($qty, 0), $stock);
        }

        // При додаванні — враховуємо вже наявне в кошику
        $inCart = $this->countItem($product);

        return min($qty, max(0, $stock - $inCart));
    }

    /**
     * Актуалізація наявності, доступності та цін товарів у кошику.
     * Використовує $item->model (associate) для отримання актуальних даних.
     *
     * @return bool true якщо хоча б один товар змінено
     */
    public function updateAvailabilityAndPrices(): bool
    {
        $items = $this->content();

        if ($items->isEmpty()) {
            return false;
        }

        $changed = false;

        foreach ($items as $rowId => $item) {
            /** @var Product|null $product */
            $product = $item->model;

            // Якщо товар видалено або неактивний — прибираємо з кошика
            if (! $product || ! $product->isActiveForOrder()) {
                $this->remove($rowId);
                $changed = true;

                continue;
            }

            $productPrice = $product->getPrice();

            // Оптова ціна (якщо є)
            $wholesalePrices = $product->getWholesalePrices();
            $effectivePrice = $wholesalePrices
                ? $this->getPriceBasedOnQuantity($item->qty, $wholesalePrices, $productPrice)
                : $productPrice;

            // Якщо в кошику більше ніж є в наявності
            if ($item->qty > $product->getQuantity()) {
                $this->update($rowId, [
                    'qty'     => $product->getQuantity(),
                    'options' => array_merge($item->options->toArray(), ['qtyOld' => $item->qty]),
                ]);
                $changed = true;
            }

            // Якщо ціна змінилась
            if ((float) $item->price !== $effectivePrice) {
                $this->update($rowId, ['price' => $effectivePrice]);
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * Загальна сума по поточних цінах моделей (не цінам кошика).
     */
    public function getSubTotalByModelPrice(): float
    {
        return $this->content()->reduce(function (float $total, CartItem $item) {
            $price = $item->model ? $item->model->getPrice() : (float) $item->price;

            return $total + ($price * $item->qty);
        }, 0.0);
    }

    /**
     * Ціна товару залежно від кількості (оптові інтервали).
     * $intervals: [ minQty => price, ... ] (ключ = мінімальна кількість, значення = ціна)
     */
    public function getPriceBasedOnQuantity(int $qty, array $intervals, float $default): float
    {
        ksort($intervals);
        $selected = $default;

        foreach ($intervals as $minQty => $price) {
            if ($qty >= $minQty) {
                $selected = (float) $price;
            }
        }

        return $selected;
    }

    /**
     * Наступний ціновий інтервал (для підказки "ще N шт — і буде дешевше").
     */
    public function getNextPriceInterval(int $qty, array $intervals): ?array
    {
        ksort($intervals);

        foreach ($intervals as $minQty => $price) {
            if ($minQty > $qty) {
                return ['minQty' => $minQty, 'price' => (float) $price];
            }
        }

        return null;
    }

    /**
     * Чи всі товари на "довірених" складах (для швидкої оплати).
     * Stub — реалізується при підключенні складського модуля.
     */
    public function allItemsOnTrustedWarehouses(): bool
    {
        return $this->content()->every(fn ($item) => (bool) ($item->model?->is_on_trusted_warehouse ?? true));
    }

    // ─── Pipeline ────────────────────────────────────────────────────

    /**
     * Додати товар через Pipeline (розширюється через config/cart.php).
     * Додаткові pipes передаються через $extraPipes для бізнес-специфічної логіки.
     *
     * @param  list<class-string>  $extraPipes
     */
    public function addThroughPipeline(
        Product $product,
        int $qty,
        array $options = [],
        array $extraPipes = [],
    ): CartContext {
        $context = new CartContext(
            product: $product,
            requestedQty: $qty,
            cartInstance: $this->instance,
            options: $options,
        );

        $pipes = array_merge(
            config('cart.pipes.add', []),
            $extraPipes,
        );

        return app(Pipeline::class)
            ->send($context)
            ->through($pipes)
            ->thenReturn();
    }
}

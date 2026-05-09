<?php

namespace App\Services\Cart;

use App\Jobs\StoreUnfinishedBasket;
use App\Models\UnfinishedBasket;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Linecore\Shoppingcart\Facades\Cart;

class UnfinishedBasketService
{
    private const COOKIE_NAME = 'unfinished_basket';

    private const COOKIE_DAYS = 30;

    public function getCookie(): ?string
    {
        return request()->cookie(self::COOKIE_NAME);
    }

    public function saveCookie(string $hash): void
    {
        cookie()->queue(
            cookie(self::COOKIE_NAME, $hash, self::COOKIE_DAYS * 24 * 60)
        );
    }

    public function getOrCreateBasket(): UnfinishedBasket
    {
        $hash = $this->getCookie() ?? Str::uuid()->toString();
        $basket = UnfinishedBasket::query()->firstOrCreate(
            ['hash_basket' => $hash],
            ['user_id' => auth()->id()],
        );

        if (! $this->getCookie()) {
            $this->saveCookie($hash);
        }

        return $basket;
    }

    public function saveRelationProducts(): void
    {
        $basket = $this->getOrCreateBasket();

        $items = Cart::content()->map(fn ($item) => [
            'product_id' => $item->id,
            'count' => $item->qty,
            'price' => $item->price,
            'options' => $item->options->toArray() ?: null,
        ])->values()->all();

        $basket->saveProducts($items);
    }

    public function dispatchSave(): void
    {
        // dispatchSync навмисно: saveRelationProducts() встановлює cookie() під час запиту.
        // Async-варіант не має доступу до HTTP-відповіді → cookie не зберігається → дублювання кошиків.
        StoreUnfinishedBasket::dispatchSync();
    }

    public function delete(): void
    {
        if ($hash = $this->getCookie()) {
            UnfinishedBasket::query()->where('hash_basket', $hash)->delete();
        }
    }

    public function getProducts(): Collection
    {
        if (! $hash = $this->getCookie()) {
            return collect();
        }

        return UnfinishedBasket::query()
            ->with('products.product')
            ->where('hash_basket', $hash)
            ->first()
            ?->products ?? collect();
    }
}

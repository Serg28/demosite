<?php

namespace App\Livewire\Cart;

use App\Actions\Cart\RemoveFromCartAction;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

class Drawer extends Component
{
    public bool $isOpen = false;

    #[On('cart-changed')]
    public function refresh(): void {}

    #[On('open-cart-drawer')]
    public function open(CartService $cartService): void
    {
        $this->isOpen = true;

        $changed = $cartService->updateAvailabilityAndPrices();
        if ($changed) {
            $this->dispatch('cart-changed', count: Cart::count(), action: 'refresh', products: []);
        }

        $this->dispatchViewCart();
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function remove(string $rowId, RemoveFromCartAction $action): void
    {
        $result = $action->handle($rowId);
        $this->dispatch('cart-changed', count: $result->count, action: $result->action, products: $result->products);
    }

    public function render(): View
    {
        $items = Cart::content();

        return view('livewire.cart.drawer', [
            'items'    => $items,
            'products' => $this->loadProducts($items),
            'total'    => Cart::total(2, '.', ''),
            'subtotal' => Cart::subtotal(2, '.', ''),
        ]);
    }

    private function loadProducts(Collection $items): Collection
    {
        if ($items->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->cartFields()
            ->whereIn('id', $items->pluck('id')->unique()->all())
            ->get()
            ->keyBy('id');
    }

    private function dispatchViewCart(): void
    {
        $items = Cart::content();
        if ($items->isEmpty()) {
            return;
        }

        $ga4Items = $items->map(function ($item) {
            /** @var \App\Models\Product|null $product */
            $product = $item->model;

            return [
                'item_id'   => $product ? $product->getArticle() : (string) $item->id,
                'item_name' => $item->name,
                'price'     => (float) $item->price,
                'quantity'  => (int) $item->qty,
            ];
        })->values()->all();

        $this->dispatch('view-cart', items: $ga4Items);
    }
}

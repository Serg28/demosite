<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use App\Services\Basket;
use App\Traits\CartOpener;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Addtocart extends Component
{
    use CartOpener;

    private Basket $basketService;

    public string|null $options;

    public int|null $quantity = 1;

    #[Locked]
    public string $view = 'livewire.cart.addtocart';

    public $product;

    //public $productId;

    public function boot(Basket $basketService): void
    {
        $this->basketService = $basketService;
    }

    #[On('reload-addtocart-buttons')]
    public function reloadButton()
    {
    }

    #[On('product-input-quantity-set')]
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    //Добавление товара в корзину
    public function addToCart(Product $product, $quantity = null, $jsonOptions = ''): void
    {
        $quantity = $quantity ?: $this->quantity;

        $response = $this->basketService->add($product, $product->title, $quantity, $product->getPrice(), 0,
            $jsonOptions);

        $this->dispatch('cart-product-added', product: $this->product, response: $response);
        $this->dispatch('cart-changed');
        //$this->dispatch('reload-addtocart-buttons');
    }

    public function render()
    {
        return view($this->view, ['product' => $this->product]);
    }
}

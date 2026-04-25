<?php

namespace App\Livewire\Cart;

use App\Services\Basket;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class Content extends ModalComponent
{
    public string|null $title;
    public string $rowId;
    public string $qty;
    public array $quantities = [];
    public string $view = 'livewire.cart.content';
    private Basket $basketService;

    public function boot(Basket $basketService)
    {
        $this->basketService = $basketService;

        //Сразу пересчитываем и обновляем состав корзины
        once(function () {
            $this->basketService->updateAvailabilityAndPrices();
        });
    }

    public function mount()
    {
        $this->quantities = $this->basketService->getQuantitiesForProducts();
    }

    #[On('cart-changed')]
    public function loadCart(): void
    {
        $this->quantities = $this->basketService->getQuantitiesForProducts();

        if (empty($this->quantities)) {
            $this->dispatch('reload-current-page');
        }

        //$this->dispatch('reload-addtocart-buttons');
    }

    public static function modalMaxWidth(): string  {
        return 'cart-popup';
    }

    public static function modalMaxWidthClass(): string  {
        return 'cart-popup';
    }

    //Обновление количества товара в корзине
    //Метод автоматически срабатывает при изменении поля quantity.
    public function updatedQuantities($quantity, $rowIdProduct): void
    {
        // Проверка на пустое значение
        if ($quantity === '' || $quantity < 1) {
            $quantity = 1;
        }

        $response = $this->basketService->update($rowIdProduct, $quantity);
        //$this->dispatch('cart-product-updated', rowId: $rowIdProduct, response: $response);
        //$this->dispatch('cart-changed');
        $this->dispatch('cart-changed', rowId: $rowIdProduct, response: $response);
    }

    //Метод реагирует на клик кнопки плюс количества (или на событие) и вызывает метод обновления товара
    #[On('cart-product-increment-qty')]
    public function incrementQuantity($rowId = '', $quantity = null): void
    {
        if ($rowId) {
            $newQty = max((int)$quantity, 1);
            $updatedQty = $this->quantities[$rowId] + $newQty;

            $this->updatedQuantities($updatedQty, $rowId);
        }
    }

    //Метод реагирует на клик кнопки минус количества (или на событие) и вызывает метод обновления товара
    #[On('cart-product-decrement-qty')]
    public function decrementQuantity($rowId = '', $quantity = null): void
    {
        if ($rowId) {
            $newQty = max((int)$quantity, 1);
            $updatedQty = $this->quantities[$rowId] - $newQty;

            if ($updatedQty > 0) {
                $this->updatedQuantities($updatedQty, $rowId);
            } else {
                $this->remove($rowId);
            }
        }
    }

    /*
    public function add($rowId){

    }*/

    //Метод удаления товара из корзины
    public function remove($rowIdProduct){
        $response = $this->basketService->remove($rowIdProduct);
        //$this->dispatch('cart-product-deleted', rowId: $rowIdProduct, response: $response);
        //$this->dispatch('cart-changed');
        $this->dispatch('cart-changed', rowId: $rowIdProduct, response: $response);
    }

    /*
    #[On('cart-product-updated')]
    public function update($rowId){

    }*/

    public function destroy($rowId){

    }

    public function render()
    {
        $cart = [
            'cartCount' => Cart::count(),
            'productsInCart' => Cart::content(),
            'cartTotal' => Cart::total(),
            'cartSubTotal' => Cart::subtotal()
        ];

        return view($this->view)->with($cart);
    }
}

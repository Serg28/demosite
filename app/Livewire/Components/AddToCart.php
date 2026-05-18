<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Validate;
use Livewire\Component;

class AddToCart extends Component
{
    #[Validate('required|integer|min:1')]
    public int $productId;

    #[Validate('required|string')]
    public string $productName;

    #[Validate('required|numeric|min:0')]
    public float $productPrice;

    #[Validate('required|integer|min:1')]
    public int $quantity = 1;

    public bool $isAdded = false;

    public function addToCart(): void
    {
        $this->validate();

        try {
            \Cart::add($this->productId, $this->productName, $this->quantity, $this->productPrice);
            $this->isAdded = true;
            $this->quantity = 1;

            $this->dispatch('cartUpdated');
            $this->dispatch('notify',
                type: 'success',
                title: __t('Успешно'),
                message: __t('Товар добавлен в корзину')
            );
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                title: __t('Ошибка'),
                message: __t('Ошибка при добавлении в корзину')
            );
        }
    }

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('livewire.components.add-to-cart');
    }
}

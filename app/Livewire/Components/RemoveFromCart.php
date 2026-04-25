<?php

namespace App\Livewire\Components;

use Livewire\Component;

class RemoveFromCart extends Component
{
    public string $rowId;

    public bool $isRemoving = false;

    public function mount(string $rowId): void
    {
        $this->rowId = $rowId;
    }

    public function remove(): void
    {
        $this->isRemoving = true;

        try {
            \Cart::remove($this->rowId);

            $this->dispatch('cartUpdated');
            $this->dispatch('notify',
                type: 'success',
                title: __t('Удалено'),
                message: __t('Товар удален из корзины')
            );

            $this->isRemoving = false;
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                title: __t('Ошибка'),
                message: __t('Ошибка при удалении товара')
            );
            $this->isRemoving = false;
        }
    }

    public function render()
    {
        return view('livewire.components.remove-from-cart');
    }
}

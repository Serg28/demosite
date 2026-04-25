<?php

namespace App\Livewire\Checkout;

use App\Livewire\Abstract\SelectSearchableAjax;
use Livewire\Attributes\Modelable;

class SelectCity extends SelectSearchableAjax
{
    //Значение по-умолчанию для $model
    public mixed $defaultValue = 0;

    public string|null $view = 'livewire.checkout.select-searchable-cities';

    //Установка значений выбранной опции и передача ее в родительский компонент
    /*public function select($value): void
    {
        if(is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }

        $this->value = $value;
        $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        $this->dispatch('cart-changed');
    }*/

    public function select($value): void
    {
        if(is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }
        if(is_array($value)) {
            $value=array_values($value);
        }
        $this->value = $value;
        $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        $this->dispatch('checkout-city-changed', city_id: $value);
    }
}
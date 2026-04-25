<?php

namespace App\Livewire\Abstract;

use App\Livewire\Checkout\Checkout;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Support\Collection;

abstract class SelectSearchableAjax extends Component
{

    //Название поля родительской модели, куда будет передаваться значение после установки
    public string $model;

    #[Modelable]
    public $value;

    //Текст плейсхолдера - может меняться в зависимости от логики
    public string|null $placeholder = '';

    //Исходный текст плейсхолдера - не меняется
    public string|null $defaultPlaceholder;

    //Значение по-умолчанию для $model
    public mixed $defaultValue = 0;

    public string|null $view = 'livewire.checkout.select-searchable';

    public function mount($defaultValue = 0, $placeholder = ''): void
    {
        $this->value = ($defaultValue) ?: null;
        $this->defaultPlaceholder = $placeholder;
    }

    public function updating($property, $value): void
    {
        if(is_array($value) && isset($value[$property])) {
            $value = $value[$property];
        }
        $this->value = $value;
        if ($property === $this->model) {
            $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        }
    }

    //Установка значений выбранной опции и передача ее в родительский компонент
    public function select($value): void
    {
        if(is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }
        $this->value = $value;
        $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        $this->dispatch('cart-changed');
    }

    public function placeholder() {
        return view('livewire.checkout.select-searchable-empty');
    }

    public function render()
    {
        return view($this->view);
    }
}

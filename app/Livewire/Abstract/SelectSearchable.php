<?php

namespace App\Livewire\Abstract;

use App\Livewire\Checkout\Checkout;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Support\Collection;

abstract class SelectSearchable extends Component implements SelectSearchableInterface
{

    //Название поля родительской модели, куда будет передаваться значение после установки
    public string $model;

    //Поисковый запрос и последующее выбранное значение из списка
    public string|null $text = null;

    //Последнее выбранное текстовое значение опции
    public string|null $textLast = null;

    public string|null $value;

    //Последнее выбранное значение опции
    public string|null $valueLast = '';

    //Текст плейсхолдера - может меняться в зависимости от логики
    public string|null $placeholder = '';

    //Исходный текст плейсхолдера - не меняется
    public string|null $defaultPlaceholder;

    //Значение по-умолчанию для $model
    public mixed $defaultValue = 0;

    //Список полученных значений для вывода options в шаблоне
    public Collection $options;

    //Состояние выпадающего списка
    public bool $show = false;

    //Состояние сообщения об отсутствии результатов
    public bool $showNoResults;

    //Состояние сообщения с приглашением ввода текста
    public bool $showStart;

    public string|null $view = 'livewire.checkout.select-searchable';

    public $checkoutErrors = null;

    //Список значений. Можно запросами
    /*public function options($searchTerm = null): Collection
    {
        //example
        //return collect([
        //    ['value' => '1', 'text' => 'Text 1'],
        //    ['value' => '2', 'text' => 'Text 2']
        //]);
        //return collect([]);
    }*/

    public function mount($defaultValue, $placeholder): void
    {
        $this->value = ($defaultValue) ?: null;
        $this->defaultPlaceholder = $placeholder;
    }

    public function updating($property, $value): void
    {
        if (($property === 'text') && $this->textLast !== $value) {
            $this->value = $this->text = $value = '';
            $property = $this->model;
        }
        if ($property === $this->model) {
            $this->dispatch('checkout-set-property', property: $property, value: $value)->to(Checkout::class);
        }
    }

    public function restoreLastValue(): void
    {
        if ($this->textLast!==$this->text && empty($this->value) && !empty($this->text)) {
            //$this->text = $this->textLast;  //восстановление последнего значения
            //$this->value = $this->valueLast;  //восстановление последнего значения
            $this->text = $this->value = ''; //вариант со сбросом, если значение некорректное
            $this->dispatch('checkout-set-property', property: $this->model, value: $this->value)->to(Checkout::class);
        }
    }

    //Получение текстового значения опции
    private function getText() {
        return $this->text ?? ($this->defaultValue && isset($this->options[$this->defaultValue]) ? $this->options[$this->defaultValue] : null);
    }

    //Получение value опции
    private function getValue() {
        return $this->value ?? ($this->defaultValue && isset($this->options[$this->defaultValue]) ? $this->defaultValue : null);
    }

    //Установка значений выбранной опции и передача ее в родительский компонент
    public function select($value, $text): void
    {
        $this->value = $this->valueLast = $value;
        $this->text = $this->textLast = $text;
        $this->show = false; // Закрыть выпадающий список после выбора
        $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        $this->dispatch('cart-changed');
    }

    public function placeholder() {
        return view('livewire.checkout.select-searchable-empty');
    }

    public function render()
    {
        $this->options = $this->options($this->text);
        $this->showNoResults = ($this->options->isEmpty() && !empty($this->text));
        $this->showStart = ($this->options->isEmpty() && empty($this->text));

        $this->text = $this->getText();
        $this->value = $this->getValue();

        return view($this->view);
    }
}

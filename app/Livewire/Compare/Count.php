<?php

namespace App\Livewire\Compare;

use Livewire\Component;
use App\Services\Compare;
use Livewire\Attributes\On;

class Count extends Component
{
    private $compareCount = 0;

    public function mount(Compare $compare)
    {
        // Получаем количество товаров для сравнения при монтировании компонента
        $this->compareCount = $compare->count();
    }

    #[On('updateCompareCount')]
    public function updateCompareCount(Compare $compare)
    {
        // Обновляем количество товаров в сравнении при вызове события
        $this->compareCount = $compare->count();
    }

    public function render()
    {
        return view('livewire.compare.count', ['count' => $this->compareCount]);
    }

    public function placeholder()
    {
        return view('livewire.compare.count-empty');
    }
}

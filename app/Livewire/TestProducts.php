<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class TestProducts extends Component
{
    use WithPagination;

    public $currentPage = 1; // Текущая страница
    public $loadMoreMode = false; // Флаг для режима "Показать ещё"

    public function loadMore()
    {
        // Активируем режим подгрузки новых элементов
        $this->loadMoreMode = true;
        $this->currentPage++;
    }

    public function render()
    {
        // Загружаем данные текущей страницы
        $items = Product::paginate(10, ['*'], 'page', $this->currentPage);

        return view('livewire.product-list', [
            'items' => $items, // Возвращаем новые элементы
        ]);
    }
}





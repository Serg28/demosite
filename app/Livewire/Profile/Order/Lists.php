<?php

namespace App\Livewire\Profile\Order;

use App\Models\Product;
use App\Services\Sort\DateSorting;
use App\Services\Sort\FilterSorting;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Locked;

class Lists extends Component
{
    use WithPagination;

    #[Locked]
    private bool $more = false;
    public $dats;

    private DateSorting $sortService;

    public function boot()
    {
        $this->sortService = new DateSorting();
    }

    /**
     * Отображает компонент.
     *
     * @return View
     */
    public function render()
    {
        $perPage = $this->sortService->getCountShow() ?? 10; // Количество записей на странице

        // Устанавливаем страницу для пагинации
        //$this->setPage($this->page);

        // Получаем текущего пользователя
        $user = app('user');

        // Строим запрос на получение заказов пользователя
        $query = $user->orders()->with('products')->when($this->sortService->getOrderField(), function ($query) {
            return $query->orderBy($this->sortService->getOrderField(), $this->sortService->getOrderDirect());
        }); // Желаемая связь с продуктами

        // Получаем заказы с учетом пагинации
        $orders = $query->paginate($perPage);

        // Обновляем или добавляем данные в список заказов
        //if ($this->more) {
        //    $this->dats = $this->dats->push(...$orders->items());
        //} else {
        //    $this->dats = collect($orders->items());
        //}

        // Возвращаем данные в шаблон
        return view('livewire.profile.order.list', [
            //'list' => $this->dats,
            'list' => $orders->items(),
            'orders' => $orders,
            'filter' => $this->sortService
        ]);
    }
}

<?php

namespace App\Livewire\Profile\Order;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Lists extends Component
{
    use WithPagination;

    #[Locked]
    public bool $more = false;

    public mixed $dats = null;

    #[Url(history: true)]
    public int $page = 1;

    #[Url(history: true)]
    public string $search = '';

    private int $limit = 16;

    public function mount(): void
    {
        $this->search = request('search', '');
        $this->page   = (int) request('page', 1);
    }

    public function updatingPage(int $page): void
    {
        if ($page !== $this->page) {
            $this->page = $page;
        }
    }

    public function render(): View
    {
        $this->setPage($this->page);

        $searchTerm = $this->search;

        $query = Auth::user()->orders()
            ->with(['products.product', 'orderStatus', 'payMethod', 'delivery'])
            ->orderByDesc('id');

        if (! empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('id', 'like', "%{$searchTerm}%")
                    ->orWhere('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%")
                    ->orWhereHas('products', function ($pq) use ($searchTerm): void {
                        $pq->where('title', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $orders = $query->paginate($this->limit);

        $this->dats = $this->more
            ? $this->dats->push(...$orders->items())
            : collect($orders->items());

        return view('livewire.profile.order.list', [
            'list'   => $this->dats,
            'orders' => $orders,
        ]);
    }

    public function showMore(): void
    {
        $this->more = true;
        $this->page++;
    }
}

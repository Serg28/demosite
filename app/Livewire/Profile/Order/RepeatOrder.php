<?php

namespace App\Livewire\Profile\Order;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RepeatOrder extends Component
{
    #[Locked]
    public int $orderId = 0;

    /** @var array<int, array{id: int, title: string, count: int, price: float, picture: string|null}> */
    #[Locked]
    public array $items = [];

    public static function modalMaxWidth(): string
    {
        return 'max-w-lg';
    }

    public function mount(int $id): void
    {
        $this->orderId = $id;

        $order = Order::query()
            ->with(['products.product:id,picture,code,is_active,quantity'])
            ->findOrFail($id);

        $this->items = $order->products
            ->filter(fn ($item) => $item->product_id !== null)
            ->map(fn ($item) => [
                'id'        => $item->product_id,
                'title'     => $item->title ?? '',
                'count'     => max(1, (int) $item->count),
                'price'     => (float) $item->price,
                'picture'   => $item->product?->picture,
                'available' => $item->product?->isActiveForOrder() ?? false,
            ])
            ->values()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.profile.order.repeat-order');
    }
}

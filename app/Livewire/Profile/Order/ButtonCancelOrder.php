<?php

namespace App\Livewire\Profile\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ButtonCancelOrder extends Component
{
    #[Locked]
    public int|null $order = null;
    #[Locked]
    public int|null $orderId = null;

    #[Locked]
    public bool $show = false;

    public function mount(Order $order): void
    {
        try {
            $this->show = $order->order_status_id == OrderStatusEnum::New() && $order->is_online_payed == PaymentStatusEnum::Unpaid();
        } catch (\Exception $exception) {
            $this->show = false;
        }
        $this->orderId = $order->id;
    }

    #[On('status-changed-order')]
    public function reloadOrder($order): void
    {

    }

    public function cancel($orderId): void
    {
        $this->dispatch('set-cancel-order', order: $orderId)->to(CancelOrder::class);
    }

    public function render()
    {
        return view('livewire.profile.order.button-cancel-order');
    }


}

<?php

namespace App\Livewire\Profile\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Traits\Referrer;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CancelOrder extends Component
{
    use Referrer;

    public Order $order;

    public bool $open = false;

    #[Locked]
    public string $title = '';

    #[Locked]
    public string $message = '';

    private string $view = 'livewire.profile.order.popup-cancel-confirm-order';

    #[On('set-cancel-order')]
    public function setOrder(Order $order): void
    {
        $this->order = $order;
        $this->open = true;
    }

    public function cancelation(): void
    {
        $this->view = 'livewire.profile.order.popup-cancel-result-order';
        $this->open = true;

        try {
            //Добавить проверку юзера
            if(app('user')->id !== $this->order->user_id) {
                $this->title = __t('Помилка');
                $this->message = __t('Недостатньо прав на скасування замовлення.');
                return;
            }

            $this->order->update(['order_status_id' => OrderStatusEnum::Canceled()]);

            $this->title = __t('Успішно');
            $this->message = str_replace(['[ordernum]', '[link]'], [
                '<strong>' . $this->order->order_number . '</strong>',
                '<a href="' . route('profile.orders.completed') . '">' . __t('Завершені') . '</a>'
            ], __t('Замовлення [ordernum] успішно скасовано. Воно переміщене у розділ [link]'));

            $this->dispatch('status-changed-order', order: $this->order->id);
        } catch (\Exception $exception) {
            $this->title = __t('Помилка');
            $this->message = __t('Не вдалося скасувати замовлення. Будь ласка, спробуйте ще раз за деякий час.');
        }
    }

    public function render()
    {
        return view($this->view);
    }
}

<?php

namespace App\Livewire\Profile\Order;

use App\Actions\Order\CancelOrder as CancelOrderAction;
use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;
use Throwable;

class CancelOrder extends Component
{
    #[Locked]
    public int $orderId = 0;

    #[Locked]
    public string $state = 'confirm'; // confirm | success | error

    public string $message = '';

    /** Визначає max-width для ModalManager */
    public static function modalMaxWidth(): string
    {
        return 'max-w-md';
    }

    /**
     * @param  int  $id  передається через data-id у data-js-modal
     */
    public function mount(int $id): void
    {
        $this->orderId = $id;
    }

    public function confirm(): void
    {
        try {
            $order = Order::findOrFail($this->orderId);
            app(CancelOrderAction::class)->handle($order, Auth::user());

            $this->state   = 'success';
            $this->message = str_replace(
                '[id]',
                (string) $order->id,
                __t('Замовлення [id] успішно скасовано.')
            );

            $this->dispatch('order-cancelled', orderId: $this->orderId);

        } catch (AuthorizationException $e) {
            $this->state   = 'error';
            $this->message = $e->getMessage();
        } catch (RuntimeException $e) {
            $this->state   = 'error';
            $this->message = $e->getMessage();
        } catch (Throwable) {
            $this->state   = 'error';
            $this->message = __t('Не вдалося скасувати замовлення. Спробуйте пізніше.');
        }
    }

    public function render(): View
    {
        return view('livewire.profile.order.cancel-order');
    }
}

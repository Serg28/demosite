<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuickOrder extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build(): void
    {
        $this
            ->subject(__t('Швидке замовлення'))
            ->view('mails.quick_order')->with([
                'order' => $this->order,
                'product' => $this->order->products()->first()->product,
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAdmin extends Mailable
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
        $subject = str_replace('[number]', $this->order->id, __t('Новый заказ №[number] на сайте'));
        $this
           ->subject($subject)
            ->view('mails.order_admin')->with([
                'order' => $this->order,
            ]);
    }
}

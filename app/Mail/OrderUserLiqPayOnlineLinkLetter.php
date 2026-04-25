<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderUserLiqPayOnlineLinkLetter extends Mailable
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
           ->subject(__t('Ccылка на оплату заказа').' '.$this->order->id) //
           ->view('mails.order_user_liqpayonline_newlink')->with([
               'order' => $this->order,
           ]);
    }
}

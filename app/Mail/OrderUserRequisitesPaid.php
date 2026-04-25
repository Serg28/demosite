<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderUserRequisitesPaid extends Mailable
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
        $subject = str_replace('[number]', $this->order->id, __t('Оплата за заказ № [number] получена'));
        $this
            ->subject($subject)
            ->view('mails.order_user_requisites_paid')->with([
                'order' => $this->order,
            ]);
    }
}

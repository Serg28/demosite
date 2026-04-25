<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReminderReserveTodayOrderLetter extends Mailable
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
           ->subject(__t('Срок резерва заказа истекает сегодня'))
           ->view('mails.reserve_today_order')->with([
               'order' => $this->order,
           ]);
    }
}

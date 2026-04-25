<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReminderReserveExpiredOrderLetter extends Mailable
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
        $subject = str_replace('[number]', $this->order->id, __t('Срок резерва заказа истекает завтра'));
        $this
           ->subject($subject) //Резерв замовлення № ХХХХХ завершується завтра
           ->view('mails.reserve_expires_order')->with([
               'order' => $this->order,
           ]);
    }
}

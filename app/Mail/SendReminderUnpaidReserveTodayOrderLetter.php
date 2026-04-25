<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReminderUnpaidReserveTodayOrderLetter extends Mailable
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
           ->subject(__t('Срок резерва неоплаченного заказа истекает сегодня'))
           ->view('mails.reserve_unpaid_today_order.blade.php')->with([
               'order' => $this->order,
           ]);
    }
}

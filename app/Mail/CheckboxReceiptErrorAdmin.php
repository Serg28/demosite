<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckboxReceiptErrorAdmin extends Mailable
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
        $subject = __t('Помилка створення чеку Checkbox.ua');
        $this
           ->subject($subject)
            ->view('mails.checkbox_receipt_error_admin')->with([
                'order' => $this->order,
            ]);
    }
}

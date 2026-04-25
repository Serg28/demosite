<?php

namespace App\Mail;

use App\Models\AvailabilityOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AvailabilityOrderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private AvailabilityOrder $order;

    public function __construct(AvailabilityOrder $order)
    {
        $this->order = $order;
    }

    public function build(): void
    {
        $this
            ->subject(__t('Сообщить на наличие товара'))
            ->view('mails.availability_order')->with([
                'order' => $this->order,
            ]);
    }
}

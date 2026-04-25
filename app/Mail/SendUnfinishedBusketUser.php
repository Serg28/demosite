<?php

namespace App\Mail;

use App\Models\UnfinishedBasket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUnfinishedBusketUser extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $unfinishedBasket;

    public function __construct(UnfinishedBasket $unfinishedBasket)
    {
        $this->unfinishedBasket = $unfinishedBasket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__t('Ваша корзина ждет вас'))
            ->view('mails.unfinished_basket_user')->with([
                'unfinishedBasket' => $this->unfinishedBasket,
            ]);
    }
}

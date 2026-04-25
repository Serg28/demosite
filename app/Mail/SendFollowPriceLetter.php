<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendFollowPriceLetter extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function build(): void
    {
        $this->view('mails.follow_price')->subject(__t('Изменение цены на отслеживаемый товар'));
    }
}

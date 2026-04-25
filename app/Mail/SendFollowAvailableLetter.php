<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendFollowAvailableLetter extends Mailable
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
        $this->view('mails.follow_available')
            ->subject(__t('Товар з\'явився у наявності'));
    }
}

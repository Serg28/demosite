<?php

namespace App\Jobs;

use App\Mail\SendFollowPriceLetter;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFollowPriceLetterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $email;

    public Product $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, Product $product)
    {
        $this->email = $email;
        $this->product = $product;
        $this->onQueue('shop-emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new SendFollowPriceLetter($this->product));
    }
}

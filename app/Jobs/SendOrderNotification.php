<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        // TODO: email/SMS сповіщення про нове замовлення
    }
}

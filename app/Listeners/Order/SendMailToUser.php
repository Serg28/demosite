<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Mail\OrderUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailToUser implements ShouldQueue
{
    use SerializesModels;

    /**
     * Имя соединения, на которое должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $connection = 'redis';

    /**
     * Имя очереди, в которую должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $queue = 'shop-emails';

    public function handle(OrderCreate $event): void
    {
        Log::info('Job SendMailToUser');
        if(!empty($event->order->email)) {
            Mail::to($event->order->email)->send(new OrderUser($event->order));
        }
    }
}

<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Mail\OrderUserRequisitesLetter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailRequisitesToUser implements ShouldQueue
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
        if (!empty($event->order->email)) {
            Mail::to($event->order->email)->send(new OrderUserRequisitesLetter($event->order));
        }
    }
}

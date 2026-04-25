<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Services\Sms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class SendSms implements ShouldQueue
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
    public ?string $queue = 'shop-sms';

    private Sms $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    public function handle(OrderCreate $event): void
    {
        $this->sms->send($event->order);
    }
}

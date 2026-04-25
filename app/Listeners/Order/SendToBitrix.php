<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Events\QuickOrderCreate;
use App\Services\Bitrix;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class SendToBitrix implements ShouldQueue
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
    public ?string $queue = 'low';

    public function handle(OrderCreate|QuickOrderCreate $event): void
    {
        (new Bitrix())->sendOrder($event->order);
    }
}

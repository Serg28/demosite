<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Events\QuickOrderCreate;
use App\Services\SalesDrive;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class SendToSalesDrive implements ShouldQueue
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
        if (setting('otpravka-v-sales-drive')) {
            (new SalesDrive())->sendOrder($event->order);
        }
    }
}

<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Events\QuickOrderCreate;
use App\Services\GaFourPurchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class SendToGA4 implements ShouldQueue
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
        if (setting('checkbox_google_analytics_four')) {
            (new GaFourPurchase($event->order))->send();
        }
    }
}

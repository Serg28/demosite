<?php

namespace App\Listeners\Feedback;

use App\Events\FeedbackCreate;
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
    public ?string $queue = 'shop-emails';

    public function handle(FeedbackCreate $event): void
    {
        if (setting('otpravka-v-sales-drive')) {
            (new SalesDrive())->sendFeedback($event->feedback);
        }
    }
}

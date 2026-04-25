<?php

namespace App\Listeners\Feedback;

use App\Events\FeedbackCreate;
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

    public function handle(FeedbackCreate $event): void
    {
        (new Bitrix())->sendFeedabck($event->feedback);
    }
}

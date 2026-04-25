<?php

namespace App\Listeners\Feed;

use App\Events\FeedSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class RegenerateXmlFeedFiles implements ShouldQueue
{
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

    public function handle(FeedSaved $event): void
    {
        Artisan::call('feed:feeds_to_files');
    }
}

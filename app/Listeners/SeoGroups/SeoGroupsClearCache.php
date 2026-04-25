<?php

namespace App\Listeners\SeoGroups;

use App\Events\SeoGroupsSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SeoGroupsClearCache implements ShouldQueue
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

    public function handle(SeoGroupsSaved $event): void
    {
        Cache::tags($event->seogroups->getCacheTags())->flush();
    }
}

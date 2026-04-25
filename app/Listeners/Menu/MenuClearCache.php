<?php

namespace App\Listeners\Menu;

use App\Events\MenuSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class MenuClearCache implements ShouldQueue
{
    //use InteractsWithQueue, Queueable;

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
    public ?string $queue = 'high';

    public function handle(MenuSaved $event): void
    {
        Artisan::call('cache:clear_rebuild');
    }
}

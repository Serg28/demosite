<?php

namespace App\Listeners\Category;

use App\Events\CategorySaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class CategoryClearCache implements ShouldQueue
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
    public ?string $queue = 'high';

    public function handle(CategorySaved $event): void
    {
        //Cache::tags($event->category->getCacheTags())->flush();
        Artisan::call('cache:clear_rebuild');
    }
}

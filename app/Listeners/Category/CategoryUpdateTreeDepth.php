<?php

namespace App\Listeners\Category;

use App\Events\CategorySaved;
use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class CategoryUpdateTreeDepth implements ShouldQueue
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
    public ?string $queue = 'high';

    public function handle(CategorySaved $event): void
    {
        //if (method_exists($event->category, 'fixDepthTree')) {
            Category::fixDepthTree();
        //}
    }
}

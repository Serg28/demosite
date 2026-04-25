<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Class RegenerateProductsPreviews
 *
 * Класс задания для регенерации превью продуктов, чьи изображения были изменены.
 *
 * @package App\Jobs
 */
class RegenerateProductsPreviews implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 1200;

    public function __construct()
    {
        $this->onQueue('default');
    }

    /**
     * Запуск задания. Используется консольная команда products:rebuild_previews
     */
    public function handle(): void
    {
        Log::info('Job RegenerateProductsPreviews started');

        Artisan::call('products:rebuild_previews');

        //Artisan::call('cache:clear_rebuild');

        Log::info('Job RegenerateProductsPreviews finished');
    }
}

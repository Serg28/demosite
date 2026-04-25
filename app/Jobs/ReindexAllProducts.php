<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;

class ReindexAllProducts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 1200;

    public int $expired = 700;

    public function __construct()
    {
        $this->onQueue('shop-reindexProducts');
    }

    // Возвращаем уникальный идентификатор задачи. Это предотвратит добавление дубликатов задач в очередь
    // Если в будущем появятся различные параметры или состояния, которые могут изменять характер задачи, можно внести изменения в этот метод.
    // Чтобы этот идентификатор был по-настоящему уникальным для предотвращения дубликатов в очереди.
    public function uniqueId(): string
    {
        return 'ReindexProducts-1';
    }

    /**
     * Устанавливаем ограничение на выполнение только одного задания с указаным ИД за раз. Остальные отбрасываются
     *
     * @return array
     */
    public function middleware()
    {
        return [(new WithoutOverlapping($this->uniqueId()))->dontRelease()->expireAfter($this->expired)];
    }

    public function handle()
    {
        $lock = Cache::lock($this->uniqueId(), $this->expired);

        if ($lock->get()) {
            try {
                $this->startProcess($lock);
            } finally {
                $lock->release();
                Log::info('Job ReindexAllProducts: method handle - lock reset');
            }
        } else {
            Log::info('Job ReindexAllProducts: Failed to acquire lock.');
        }

        Log::info('Job ReindexAllProducts finished');
    }

    private function startProcess($lock): void
    {
        try {
            Log::info('Job ReindexAllProducts started');

            //Команда переиндексации всех товаров с пересозданием индекса
            \Artisan::call('elastic:reindex "App\ProductsIndexConfigurator"');

            Artisan::call('cache:clear');

        } finally {
            $lock->release();
            Log::info('Job ReindexAllProducts: method startProcess - lock reset');
        }
    }
}

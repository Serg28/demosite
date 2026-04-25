<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class MaintenanceCategories
 *
 * Класс для обслуживания каталога: характеристик, опций, связей.
 *
 * @package App\Jobs
 */
class MaintenanceCatalog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public int $expired = 1000;

    public function __construct()
    {
        $this->onQueue('low');
    }

    // Возвращаем уникальный идентификатор задачи. Это предотвратит добавление дубликатов задач в очередь
    // Если в будущем появятся различные параметры или состояния, которые могут изменять характер задачи, можно внести изменения в этот метод.
    // Чтобы этот идентификатор был по-настоящему уникальным для предотвращения дубликатов в очереди.
    public function uniqueId(): string
    {
        return 'MaintenanceCatalogJob-1';
    }

    /**
     * Handle method for rebuilding categories.
     *
     * This method performs the following tasks:
     *
     * @return void
     */
    public function handle(): void
    {
        $lock = Cache::lock($this->uniqueId(), $this->expired);

        if ($lock) {
            try {
                $this->startProcess($lock);
            } finally {
                $lock->release();
            }
        } else {
            Log::info('Job MaintenanceCatalog: Failed to acquire lock.');
        }
    }


    private function startProcess($lock): void
    {
        try {
            //Заполняем пустые слаги характеристик
            \Artisan::call('characteristic:generate-new-slugs');

            //Заполняем пустые слаги опций
            \Artisan::call("options:generate-new-slugs");

            //Проверяем и синхронизируем опции у товаров + характеристики у категорий
            //\Artisan::call('characteristic:category');

            //Заполняем пустые слаги товаров
            \Artisan::call("products:generate-new-urls");

            //Команда переиндексации товаров с updated=1 и сброс в updated=0
            \Artisan::call('products:reindex-updated');

            //Сбрасываем кеш
            \Artisan::call('cache:clear');

            Log::info('Catalog maintenance successfully!');
        } finally {
            $lock->release();
            Log::info('Job MaintenanceCatalog: method startProcess - lock reset');
        }
    }
}
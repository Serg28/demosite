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
 * Класс для перестроения и обслуживания категорий товаров.
 *
 * @package App\Jobs
 */
class MaintenanceCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $expired = 100;

    public function __construct()
    {
        $this->onQueue('low');
    }

    // Возвращаем уникальный идентификатор задачи. Это предотвратит добавление дубликатов задач в очередь
    // Если в будущем появятся различные параметры или состояния, которые могут изменять характер задачи, можно внести изменения в этот метод.
    // Чтобы этот идентификатор был по-настоящему уникальным для предотвращения дубликатов в очереди.
    public function uniqueId(): string
    {
        return 'MaintenanceCategoriesJob-1';
    }

    /**
     * Handle method for rebuilding categories.
     *
     * This method performs the following tasks:
     * 1. Fixes the structure of categories.
     * 2. Generates new slugs and URLs for empty categories.
     * 3. Clears the cache.
     * 4. Logs the successful rebuild of categories.
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
            Log::info('Job MaintenanceCategories: Failed to acquire lock.');
        }
    }


    private function startProcess($lock): void
    {
        try {
            //Исправляем структуру категорий
            \Artisan::call('categories:fix');

            //Заполняем пустые слаги и урлы категорий
            \Artisan::call('categories:generate-new-urls');

            //Сбрасываем кеш
            \Artisan::call('cache:clear');

            Log::info('Categories maintenance successfully!');
        } finally {
            $lock->release();
            Log::info('Job MaintenanceCategories: method startProcess - lock reset');
        }
    }
}
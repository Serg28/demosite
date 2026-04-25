<?php

namespace App\Jobs;

use App\Services\ElasticsearchIndexUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;

class ReactivateProductsIndexByIds implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $productIds;

    public int $expired = 180;

    public function __construct(array $productIds = [])
    {
        ini_set('memory_limit', '512M');
        $this->productIds = $productIds;

        $this->onQueue('shop-reindexProducts');
    }

    // Возвращаем уникальный идентификатор задачи. Это предотвратит добавление дубликатов задач в очередь
    // Если в будущем появятся различные параметры или состояния, которые могут изменять характер задачи, можно внести изменения в этот метод.
    // Чтобы этот идентификатор был по-настоящему уникальным для предотвращения дубликатов в очереди.
    public function uniqueId(): string
    {
        return 'ReindexAllProducts-1';
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

    public function handle(): void
    {
        Log::info('---Elasticsearch reactivate products started');

        $lock = Cache::lock($this->uniqueId(), $this->expired);

        if ($lock->get()) {
            try {
                $this->startProcess($lock);
            } finally {
                $lock->release();
                Log::info('Elasticsearch reactivate products: method handle - lock reset');
            }
        } else {
            Log::info('Elasticsearch reactivate products: Failed to acquire lock.');
        }

        Log::info('---Elasticsearch reactivate products finished');
    }

    private function startProcess($lock)
    {
        $index = new ElasticsearchIndexUpdate();

        // Получаем текущий индекс
        $currentIndex = $index->getCurrentIndex();

        //Если есть индекс, реактивируем продукты
        if ($currentIndex) {
            try {
                $result = $index->updateByIds(['is_active' => 1], $this->productIds);

                if (!empty($result) && count($this->productIds) > 0) {
                    Log::info('Elasticsearch reactivate '.count($this->productIds).' products: is_active=1 completed. Result: ' . json_encode($result, JSON_PRETTY_PRINT));

                    if ($result = $index->updateExcludingIds(['is_active' => 0], $this->productIds)) {
                        Log::info('Elasticsearch reactivate products: is_active=0 completed. Result: ' . json_encode($result, JSON_PRETTY_PRINT));
                    }
                }
                Artisan::call('cache:clear_rebuild');
            } catch (\Exception $e) {
                Log::error('Elasticsearch reactivate products failed with error: ' . $e->getMessage());
            } finally {
                $lock->release();
                Log::info('Elasticsearch reactivate products: method startProcess - lock reset');
            }
        } else {
            Log::info('Elasticsearch reactivate: Product index not found. Starting re-create product index');
            ReindexAllProducts::dispatch();
        }
    }
}

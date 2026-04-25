<?php

namespace App\Jobs;

use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegenerateProductCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Product $product)
    {
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job RegenerateProductCache for product ' . $this->product->id . ' started');
        $cacheTag = $this->product->getCacheKey();
        //Сбрасываем кеш товара
        Cache::tags([
            'product_card_' . $cacheTag,
            'product_labels_' . $cacheTag,
            'product.otherPictures.' . $cacheTag,
            'product_card_characteristics_' . $cacheTag,
            'product_card_gallery_' . $cacheTag
        ])->flush();
        //TODO: перегенерируем блоки товаров для каталога и карточки товара

        Log::info('Job RegenerateProductCache for product ' . $this->product->id . ' finished');
    }
}

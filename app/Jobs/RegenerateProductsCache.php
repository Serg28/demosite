<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegenerateProductsCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('low');
    }

    public function handle()
    {
        Log::info('Job RegenerateProductsCache started');
        //Сбрасываем кеш товаров (возможно, еще другие)
        Cache::tags(['tree', 'category', 'products', 'elasticsearch', 'category_filter'])->flush();
        //TODO: перегенерируем блоки товаров для каталога и карточки товара

        Log::info('Job RegenerateProductsCache finished');
    }
}

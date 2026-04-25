<?php

namespace App\Console\Commands;

use App\Jobs\ReindexAllProducts;
use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;

class CheckProductIndexExists extends Command
{
    use DispatchesJobs;

    private static Client $client;
    protected $signature = 'search:check_product_index';

    protected $description = 'Checking the existence of a product index and if not, start re-indexing';

    private $elasticsearch;

    public function __construct(Client $elasticsearch, Product $product)
    {
        ini_set('memory_limit', '512M');
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
        $this->product = $product;
    }


    /*С делением на чанки и без простоя*/
    public function handle(): void
    {
        try {
            // Получаем имя текущего индекса
            $currentIndex = $this->product->getSearchIndex();

            // Получаем текущий индекс
            $index = $this->elasticsearch->indices()->get(['index' => $currentIndex]);

            // Если не существует, запускаем переиндексацию
            if (!count($index)) {

                ReindexAllProducts::dispatch();
                $this->info('The index does not exist. Re-creating...');
                Log::info('Command CheckProductIndexExists: Product index not found. Starting re-create product index');

                return;
            }


            $this->info('Done! The index exists!');
            Log::info('Command CheckProductIndexExists: Product index found');

        } catch (\Exception $e) {
            ReindexAllProducts::dispatch();

            $this->info('The index does not exist. Re-creating...');
            Log::info('Command CheckProductIndexExists: Product index not found. Starting re-create product index');

            return;
        }
    }
    //----

}

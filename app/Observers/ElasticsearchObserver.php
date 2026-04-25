<?php

namespace App\Observers;

use App\Jobs\ReindexProduct;
use App\Models\Product;
use Elasticsearch\Client;

class ElasticsearchObserver
{
    private Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved(Product $model): void
    {
        ReindexProduct::dispatch($model)->delay(3);
    }

    public function deleted(Product $model): void
    {
        $this->elasticsearch->delete([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            'id' => $model->getKey(),
        ]);
    }
}

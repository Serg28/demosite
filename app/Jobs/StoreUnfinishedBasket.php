<?php

namespace App\Jobs;

use App\Services\UnfinishedBasketService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreUnfinishedBasket implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('low');
    }

    public function handle(UnfinishedBasketService $service): void
    {
        $service->saveRelationProducts();
    }
}

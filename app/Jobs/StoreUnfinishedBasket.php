<?php

namespace App\Jobs;

use App\Services\UnfinishedBasketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreUnfinishedBasket implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected UnfinishedBasketService $basketService;

    public function __construct(UnfinishedBasketService $basketService)
    {
        $this->basketService = $basketService;
        $this->onQueue('low');
    }

    public function handle(): void
    {
        $this->basketService->saveRelationProducts();
    }
}

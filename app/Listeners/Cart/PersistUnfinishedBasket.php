<?php

namespace App\Listeners\Cart;

use App\Services\UnfinishedBasketService;

final class PersistUnfinishedBasket
{
    public function __construct(private readonly UnfinishedBasketService $service) {}

    public function handle(): void
    {
        $this->service->dispatchSave();
    }
}

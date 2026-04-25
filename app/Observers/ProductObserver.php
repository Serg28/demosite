<?php

namespace App\Observers;

use App\Jobs\RegenerateProductCache;
use App\Models\Product;
use App\Services\FollowAvailableLetter;
use App\Services\FollowPriceLetter;

class ProductObserver
{
    private FollowPriceLetter $followPriceLetter;

    private FollowAvailableLetter $followAvailableLetter;

    public function __construct(FollowPriceLetter $followPriceLetter, FollowAvailableLetter $followAvailableLetter)
    {
        $this->followPriceLetter = $followPriceLetter;
        $this->followAvailableLetter = $followAvailableLetter;
    }

    public function updated(Product $product): void
    {
        //TODO: логика при обновлении товара - следить за ценой
        //if ($product->isDirty('price')) {
        //    $this->sendEmailFollowingPriceUser($product);
        //}
        //TODO: логика при обновлении товара - уведомление о поступлении товара
        //if ($product->getOriginal('product_status_id') == 4 && $product->product_status_id == 1) {
        //    $this->sendEmailFollowingAvailableUser($product);
        //}

        //RegenerateProductCache::dispatch($product);
    }

    private function sendEmailFollowingPriceUser(Product $product): bool
    {
        return $this->followPriceLetter->send($product);
    }

    private function sendEmailFollowingAvailableUser(Product $product): bool
    {
        return $this->followAvailableLetter->send($product);
    }
}

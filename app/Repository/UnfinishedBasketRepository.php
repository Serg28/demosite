<?php

namespace App\Repository;

use App\Models\UnfinishedBasket;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UnfinishedBasketRepository
{
    public function create(string $hash): UnfinishedBasket
    {
        return UnfinishedBasket::firstOrCreate(
            ['hash_basket' => $hash],
            ['hash_basket' => $hash, 'user_id' => app('user') ? app('user')->id : null]
        );
    }

    public function delete(?string $hash)
    {
        return UnfinishedBasket::where(['hash_basket' => $hash])->delete();
    }

    public function getProducts(?string $hash): Collection
    {
        if (! $hash) {
            return collect([]);
        }

        $basket = UnfinishedBasket::where('hash_basket', $hash)->first();

        if ($basket) {
            return $basket->products;
        }

        return collect([]);
    }

    public function getUnfinishedBaskets(): Collection
    {
        return UnfinishedBasket::has('user')
            ->with(['user', 'products'])
            ->whereBetween('updated_at', [$this->getDispatchDay()->startOfDay(), $this->getDispatchDay()->endOfDay()])
            ->get();
    }

    private function getDispatchDay(): Carbon
    {
        return Carbon::now()->subDay(3);
    }
}

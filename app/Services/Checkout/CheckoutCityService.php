<?php

namespace App\Services\Checkout;

use App\Models\City;
use Illuminate\Support\Collection;

class CheckoutCityService
{
    private const MIN_QUERY_LENGTH = 2;

    private const DEFAULT_LIMIT = 15;

    public function search(string $q, int $limit = self::DEFAULT_LIMIT): Collection
    {
        $q = trim($q);

        if (mb_strlen($q) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        return City::query()
            ->active()
            ->where(function ($query) use ($q) {
                $query->where('title->ua', 'like', $q.'%')
                    ->orWhere('title->ru', 'like', $q.'%');
            })
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) ASC")
            ->limit($limit)
            ->get()
            ->map(fn (City $city) => ['id' => $city->id, 'title' => $city->t('title')])
            ->values();
    }
}

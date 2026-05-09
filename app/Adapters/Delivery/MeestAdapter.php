<?php

namespace App\Adapters\Delivery;

use App\Contracts\CarrierAdapter;
use App\Services\Delivery\CityResolver;

class MeestAdapter implements CarrierAdapter
{
    public function __construct(private readonly CityResolver $cityResolver) {}

    public function carrier(): string
    {
        return 'meest';
    }

    public function label(): string
    {
        return 'Meest';
    }

    public function syncCities(callable $onProgress): void
    {
        // TODO: Meest API — /api/v1/localities
    }

    public function syncWarehouses(callable $onProgress): void
    {
        // TODO: Meest API — /api/v1/branches
    }
}

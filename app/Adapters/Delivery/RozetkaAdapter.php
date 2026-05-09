<?php

namespace App\Adapters\Delivery;

use App\Contracts\CarrierAdapter;
use App\Services\Delivery\CityResolver;

class RozetkaAdapter implements CarrierAdapter
{
    public function __construct(private readonly CityResolver $cityResolver) {}

    public function carrier(): string
    {
        return 'rozetka';
    }

    public function label(): string
    {
        return 'Rozetka';
    }

    public function syncCities(callable $onProgress): void
    {
        // TODO: Rozetka delivery points API
    }

    public function syncWarehouses(callable $onProgress): void
    {
        // TODO: Rozetka delivery points — /api/v2/delivery-points
    }
}

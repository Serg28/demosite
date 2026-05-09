<?php

namespace App\Adapters\Delivery;

use App\Contracts\CarrierAdapter;
use App\Services\Delivery\CityResolver;

class JustinAdapter implements CarrierAdapter
{
    public function __construct(private readonly CityResolver $cityResolver) {}

    public function carrier(): string
    {
        return 'justin';
    }

    public function label(): string
    {
        return 'Justin';
    }

    public function syncCities(callable $onProgress): void
    {
        // TODO: Justin API — getCities
    }

    public function syncWarehouses(callable $onProgress): void
    {
        // TODO: Justin API — getBranches
    }
}

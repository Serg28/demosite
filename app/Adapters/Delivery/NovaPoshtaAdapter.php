<?php

namespace App\Adapters\Delivery;

use App\Contracts\CarrierAdapter;
use App\Services\Delivery\CityResolver;

class NovaPoshtaAdapter implements CarrierAdapter
{
    public function __construct(private readonly CityResolver $cityResolver) {}

    public function carrier(): string
    {
        return 'np';
    }

    public function label(): string
    {
        return 'Нова Пошта';
    }

    public function syncCities(callable $onProgress): void
    {
        // TODO: NP API v2 — POST https://api.novaposhta.ua/v2.0/json/
        // method: Address.getCities, ref (UUID) → city_carrier_codes
    }

    public function syncWarehouses(callable $onProgress): void
    {
        // TODO: NP API v2 — Address.getWarehouses
        // TypeOfWarehouseRef: branch = '9a68df70-4c...', poshtamat = '841339c7-59...'
        // Upsert in delivery_warehouses where carrier='np'
    }
}

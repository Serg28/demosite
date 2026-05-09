<?php

namespace App\Adapters\Delivery;

use App\Contracts\CarrierAdapter;
use App\Services\Delivery\CityResolver;

class UkrposhtaAdapter implements CarrierAdapter
{
    public function __construct(private readonly CityResolver $cityResolver) {}

    public function carrier(): string
    {
        return 'ukrposhta';
    }

    public function label(): string
    {
        return 'Укрпошта';
    }

    public function syncCities(callable $onProgress): void
    {
        // TODO: КАТОТТГ довідник — katottg_code → city
        // Укрпошта не має власних міст; прив'язка через КАТОТТГ
    }

    public function syncWarehouses(callable $onProgress): void
    {
        // TODO: API Укрпошти — settlements/search + post-offices
        // Немає російської версії → title = ['ua' => '...'] (fallback в HasTranslations)
    }
}

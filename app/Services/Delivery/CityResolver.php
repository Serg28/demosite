<?php

namespace App\Services\Delivery;

use App\Contracts\TranslatorAdapter;
use App\Models\City;
use App\Models\CityCarrierCode;

class CityResolver
{
    public function __construct(private readonly TranslatorAdapter $translator) {}

    public function findOrCreate(
        string $carrier,
        string $carrierRef,
        string $titleUa,
        ?string $titleRu = null,
        ?string $katottgCode = null,
        ?string $regionUa = null,
    ): City {
        $code = CityCarrierCode::query()
            ->where('carrier', $carrier)
            ->where('ref', $carrierRef)
            ->with('city')
            ->first();

        if ($code !== null) {
            return $code->city;
        }

        $city = City::query()
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) = ?", [$titleUa])
            ->first();

        if ($city === null) {
            $title = ['ua' => $titleUa];

            if ($titleRu) {
                $title['ru'] = $titleRu;
            }

            $region = $regionUa ? ['ua' => $regionUa] : null;

            $city = City::create([
                'title'        => $title,
                'region'       => $region,
                'katottg_code' => $katottgCode,
                'is_active'    => true,
            ]);
        }

        CityCarrierCode::firstOrCreate(
            ['carrier' => $carrier, 'ref' => $carrierRef],
            ['city_id' => $city->id],
        );

        return $city;
    }
}

<?php

namespace App\Services;

use App\Models\City;
use App\Models\Delivery;
use App\Models\MeestWarehouse;
use Illuminate\Support\Facades\Log;

class MeestApi
{
    private $delivery;

    public function parse(): void
    {
        Log::info('CRON Meest start');
        $result = $this->send();

        if ($result->status) {
            $this->delivery = Delivery::where('type', 'meest')->first();
            $this->createWarehouse($result->result);
        }
        Log::info('CRON Meest end');
    }

    public function createWarehouse($data): void
    {
        foreach ($data as $item) {
            $city = City::select('id')->where('title->ua', 'like', $item->city->ua.'%')
                ->orWhere('title->ru', 'like', $item->city->ru.'%')
                ->orWhere('title->en', 'like', $item->city->en.'%')
                ->first();

            if ($city) {
                $city->deliveries()->syncWithoutDetaching([$this->delivery->id]);

                $number = '№ '.$item->num;
                $title = $this->jsonTitle(
                    $number.' '.$item->type_public->ua,
                    $number.' '.$item->type_public->ru,
                    $number.' '.$item->type_public->en
                );

                MeestWarehouse::updateOrCreate(
                    [
                        'br_id' => $item->br_id,
                    ],
                    [
                        'title' => $title,
                        'city_id' => $city->id,
                        'num' => $item->num
                    ]
                );
            }
        }
    }

    public function jsonTitle($ua, $ru, $en)
    {
        return json_encode([
            'ua' => (string) $ua,
            'ru' => (string) $ru,
            'en' => (string) $en,
        ]);
    }

    public function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://publicapi.meest.com/branches');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
}

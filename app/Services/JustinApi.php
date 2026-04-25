<?php

namespace App\Services;

use App\Models\City;
use App\Models\Delivery;
use App\Models\JustinWarehouse;
use App\Models\Region;
use Illuminate\Support\Facades\Log;

class JustinApi
{
    private $url = 'https://api.justin.ua/';

    private $delivery;

    public function parse()
    {
        Log::info('CRON Justin start');
        $this->delivery = Delivery::where('type', 'justin')->first();

        $getData = $this->getWarehouse();
        $data = json_decode($getData);

        foreach ($data->data as $item) {
            $this->createWarehouse($item->fields);
        }
        Log::info('CRON Justin end');

        return true;
    }

    public function createWarehouse($warehouse): void
    {
        $getCity = $warehouse->city->descr;

        $query = mb_convert_case($getCity, MB_CASE_TITLE, 'UTF-8');

        $regionId = $this->getRegion($warehouse->region->descr);

        $city = City::where('title->ua', 'like', $query.'%')->where('region_id', '=', $regionId)
            ->orWhere('title->ru', 'like', $query.'%')->where('region_id', '=', $regionId)
            ->orWhere('title->en', 'like', $query.'%')->where('region_id', '=', $regionId)
            ->first();

        if ($city) {
            $city->deliveries()->syncWithoutDetaching([$this->delivery->id]);

            $title = $this->jsonTitle($warehouse->street->descr.' '.trim($warehouse->houseNumber).', '.$warehouse->Depart->descr);

            JustinWarehouse::updateOrCreate([
                'uuid' => (string) $warehouse->Depart->uuid,
                'city_id' => $city->id,
            ], [
                'title' => (string) $title,
                'num' => $warehouse->departNumber
            ]);
        }
    }

    public function getRegion(string $title)
    {
        return Region::where('title->ua', 'like', $title.'%')->first()->id;
    }

    public function jsonTitle($ua)
    {
        return json_encode([
            'en' => (string) $ua,
            'ru' => (string) $ua,
            'ua' => (string) $ua,
        ]);
    }

    public function getWarehouse()
    {
        $post = [
            'keyAccount' => config('services.justin.login'),
            'sign' => sha1(config('services.justin.password').':'.date('Y-m-d')),
            'request' => 'getData',
            'type' => 'request',
            'name' => 'req_DepartmentsLang',
            'params' => [
                'language' => 'UA',
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.'justin_pms/hs/v2/runRequest');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

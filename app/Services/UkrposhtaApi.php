<?php

namespace App\Services;

use App\Models\City;
use App\Models\Delivery;
use App\Models\Region;
use App\Models\UkrposhtaWarehouse;
use Illuminate\Support\Facades\Log;

class UkrposhtaApi
{
    private Delivery $delivery;

    private $region;

    public function parse(): void
    {
        Log::info('CRON UkrPoshta start');
        $this->delivery = Delivery::where('type', 'ukrposhta')->first();

        $regions = $this->getInfoRegions();
        $this->listRegion($regions);
        Log::info('CRON UkrPoshta end');
    }

    public function listRegion($regions): void
    {
        $data = $regions->Entries->Entry;

        foreach ($data as $item) {
            if ($item->REGION_UA == 'Київ' || $item->REGION_RU == 'Киев' || $item->REGION_EN == 'Kyiv') {
                $item->REGION_UA = 'Київська';
                $item->REGION_RU = 'Kyivska';
                $item->REGION_EN = 'Киевская';
            }

            $this->region = Region::where('title->ua', 'like', $item->REGION_UA.'%')
                ->orWhere('title->ru', 'like', $item->REGION_RU.'%')
                ->orWhere('title->en', 'like', $item->REGION_EN.'%')
                ->first();

            $districts = $this->getInfoDistrict($item->REGION_ID);

            if ($districts) {
                $this->createListData($districts, 'getInfoCity', 'DISTRICT_ID');
            }
        }
    }

    public function createListData($data, $method, $id): void
    {
        $items = $data->Entries->Entry;
        foreach ($items as $item) {
            $this->$method($item->$id);
        }
    }

    public function getInfoRegions()
    {
        return $this->send('https://ukrposhta.ua/address-classifier-ws/get_regions_by_region_ua?region_name=');
    }

    public function getInfoDistrict($region_id)
    {
        return $this->send('https://ukrposhta.ua/address-classifier-ws/get_districts_by_region_id_and_district_ua?region_id='.$region_id);
    }

    public function getInfoCity($district_id): void
    {
        $cities = $this->send('https://ukrposhta.ua/address-classifier-ws/get_city_by_region_id_and_district_id_and_city_ua?district_id='.$district_id);
        if ($cities) {
            $this->createListData($cities, 'getInfoWarehouse', 'CITY_KOATUU');
        }
    }

    public function getInfoWarehouse($city_koatuu): void
    {
        $url = 'https://ukrposhta.ua/address-classifier-ws/get_postoffices_by_postcode_cityid_cityvpzid?city_koatuu='.$city_koatuu;
        $warehouses = $this->send($url);

        if (get_object_vars($warehouses->Entries)) {
            $items = $warehouses->Entries->Entry;
            foreach ($items as $item) {
                $this->createWarehouse($item, $this->region->id);
            }
        }
    }

    public function createWarehouse($warehouse, $region_id): void
    {
        $getCity = $warehouse->CITY_UA;

        $query = mb_convert_case($getCity, MB_CASE_TITLE, 'UTF-8');

        $city = City::where('title->ua', 'like', $query.'%')->where('region_id', '=', $region_id)
            ->orWhere('title->ru', 'like', $query.'%')->where('region_id', '=', $region_id)
            ->orWhere('title->en', 'like', $query.'%')->where('region_id', '=', $region_id)
            ->first();

        if ($city) {
            $city->deliveries()->syncWithoutDetaching([$this->delivery->id]);

            $street = (isset($warehouse->STREET_UA_VPZ) && ! empty($warehouse->STREET_UA_VPZ))
                ? $warehouse->STREET_UA_VPZ
                : $warehouse->POSTOFFICE_UA_DETAILS;

            $title = $this->jsonTitle('№'.$warehouse->POSTCODE.', '.$street);

            UkrposhtaWarehouse::updateOrCreate([
                'postcode' => (string) $warehouse->POSTCODE,
                'city_id' => $city->id,
            ], [
                'title' => (string) $title,
            ]);
        }
    }

    public static function send($url)
    {
        $bearer = config('services.ukrposhta.bearer');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer '.$bearer,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function jsonTitle(string $ua)
    {
        return json_encode([
            'en' => (string) $ua,
            'ru' => (string) $ua,
            'ua' => (string) $ua,
        ]);
    }
}

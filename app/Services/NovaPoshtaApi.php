<?php

namespace App\Services;

use App\Models\City;
use App\Models\NPWarehouse;
use App\Models\Region;
use App\Models\Settlement;
use Illuminate\Support\Facades\Log;

class NovaPoshtaApi
{
    public function parse()
    {
        Log::info('CRON NovaPoshta start');
        $this->NPMethods('Address', 'getAreas', 'createRegion');
        $this->NPMethods('Address', 'getCities', 'fillTable');
        $this->NPMethods('AddressGeneral', 'getWarehouses', 'createWarehouse');
        Log::info('CRON NovaPoshta end');

        return true;
    }

    public function NPMethods($npModel, $npMethod, $method): void
    {
        $xml = $this->createHeaderXml($npModel, $npMethod);
        $values = $this->getXmlNP($xml, '/'.$npModel.'/'.$npMethod);
        foreach ($values->data->item as $value) {
            $this->$method($value);
        }
    }

    public function createHeaderXml($modelName, $calledMethod, $methodProperties = '')
    {
        return '<?xml version="1.0" encoding="utf-8"?>
        <file>
	        <apiKey>'.config('services.np.api_key').'</apiKey>
	        <modelName>'.$modelName.'</modelName>
			<calledMethod>'.$calledMethod.'</calledMethod>
			'.$methodProperties.'
        </file>';
    }

    public function getXmlNP($xml, $type)
    {
        return simplexml_load_string(self::send($xml, $type));
    }

    public static function send($xml, $type)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/xml'.$type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function fillTable($info): void
    {
        $this->createSettlement($info);
        $this->createCities($info);
    }

    public function createCities($city): void
    {
        $title = $this->JsonTitle(
            $this->replaceRegionName($city->Description),
            $this->replaceRegionName($city->DescriptionRu)
        );

        $originTitle = $this->JsonTitle(
            $city->Description,
            $city->DescriptionRu
        );

        $type_id = Settlement::where('ref', $city->SettlementType)->first();
        $region_id = Region::where('ref', $city->Area)->first();

        if ($type_id && $region_id) {
            City::updateOrCreate([
                'ref' => (string) $city->Ref,
            ], [
                'origin' => (string) $originTitle,
                'type_id' => (int) $type_id->id,
                'region_id' => (int) $region_id->id,
                'title' => (string) $title,
            ]);
        }
    }

    public function createRegion($region): void
    {
        $title = $this->JsonTitle(
            $this->replaceRegion($region->Description),
            $this->replaceRegion($region->DescriptionRu)
        );

        Region::updateOrCreate([
            'ref' => (string) $region->Ref,
        ], [
            'title' => $title,
        ]);
    }

    public function createSettlement($settlement): void
    {
        $title = $this->JsonTitle(
            $settlement->SettlementTypeDescription,
            $settlement->SettlementTypeDescriptionRu
        );

        Settlement::updateOrCreate([
            'ref' => (string) $settlement->SettlementType,
        ], [
            'title' => $title,
        ]);
    }

    public function createWarehouse($warehouse): void
    {
        $city = City::where('ref', (string) $warehouse->CityRef)->first();

        $title = $this->JsonTitle(
            $this->replaceRegion($warehouse->Description),
            $this->replaceRegion($warehouse->DescriptionRu)
        );

        if ($city) {
            NPWarehouse::updateOrCreate([
                'ref' => (string) $warehouse->Ref,
                'city_id' => $city->id,
            ], [
                'title' => (string) $title,
                'num' => $warehouse->Number,
            ]);
        }
    }

    public function JsonTitle($ua, $ru)
    {
        return json_encode([
            'en' => (string) $ua,
            'ru' => (string) $ru,
            'ua' => (string) $ua,
        ]);
    }

    public function replaceRegion($title)
    {
        return  str_replace(' область', '', $title);
    }

    public function replaceRegionName($title)
    {
        $city = trim(strstr($title, '(', true));

        return $city ?: $title;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromApi
{
    private const URL_PROM = 'https://my.prom.ua/api/v1/';

    public function getOrder()
    {
        return $this->jsonDecode(
            $this->getHttp()->get(self::URL_PROM.'orders/list')->body()
        );
    }

    public function getListStatus()
    {
        return $this->jsonDecode(
            $this->getHttp()->get(self::URL_PROM.'order_status_options/list')->body()
        );
    }

    public function getListPaymentOptions()
    {
        return $this->jsonDecode(
            $this->getHttp()->get(self::URL_PROM.'payment_options/list')->body()
        );
    }

    public function getListDeliveryOptions()
    {
        return $this->jsonDecode(
            $this->getHttp()->get(self::URL_PROM.'delivery_options/list')->body()
        );
    }

    public function changeStatus(string $status, int $id)
    {
        $dataStatus = [
            'status' => $status,
            'ids' => [$id],
        ];

        if ($status === 'canceled') {
            $dataStatus['cancellation_reason'] = 'not_available';
        }

        $result = $this->getHttp()->post(self::URL_PROM.'orders/set_status', $dataStatus)->body();

        Log::info($dataStatus);
        Log::info($result);
    }

    private function getHttp()
    {
        return Http::withToken(config('services.prom.token'));
    }

    private function jsonDecode(string $json)
    {
        return json_decode($json, true);
    }
}

<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bitrix
{
    private string $url;

    private bool $sendIsActive = false;

    private string $startUrl = 'url - ';

    public function __construct()
    {
        $this->url = setting('bitrix-vebhuk');
        $this->sendIsActive = setting('otpravka-v-bitrix');
    }

    public function sendOrder(Order $order): void
    {
        if (! $this->sendIsActive || ! $this->url) {
            return;
        }
        $data = $this->prepareOrder($order);
        Log::info(implode('<br>', ['Send order to bitrix', $this->startUrl.$this->url]));
        Log::info($data);

        $response = Http::post($this->url.'crm.lead.add', $data);
        Log::info($response);
    }

    public function sendFeedabck(Feedback $feedback): void
    {
        if (! $this->sendIsActive || ! $this->url) {
            return;
        }
        $data = $this->prepareFeedback($feedback);
        Log::info(implode('<br>', ['Send feedback to bitrix', $this->startUrl.$this->url]));
        Log::info($data);
        Http::post($this->url.'crm.lead.add', $data);
    }

    private function sendProducts(Order $order, int $idLead): void
    {
        $data = [
            'id' => $idLead,
            'rows' => [$this->prepareProducts($order)[0]],
        ];
        Log::info(implode('<br>', ['Send products to bitrix', $this->startUrl.$this->url]));
        Log::info($data);
        Http::post($this->url.'crm.lead.productrows.set', $data);
    }

    private function prepareOrder(Order $order): array
    {
        return [
            'fields' => [
                'TITLE' => '№'.$order->id.' - заказ с сайта '.env('APP_URL'),
                'NAME' => $order->name ?? '',
                'CURRENCY_ID' => 'UAH',
                'OPPORTUNITY' => $order->cost,
                'HAS_EMAIL' => 'Y',
                'EMAIL' => [
                    ['VALUE' => $order->email ?? '', 'VALUE_TYPE' => 'WORK'],
                ],
                'PHONE' => [
                    ['VALUE' => $order->phone, 'VALUE_TYPE' => 'WORK'],
                ],
                'COMMENTS' => implode('<br>', $this->getProducts($order)),
                'DATE_CREATE' => $order->created_at,
                'UTM_SOURCE' => isset($order->orderUtm) ? $order->orderUtm->utm_source : '',
                'UTM_MEDIUM' => isset($order->orderUtm) ? $order->orderUtm->utm_medium : '',
                'UTM_CAMPAIGN' => isset($order->orderUtm) ? $order->orderUtm->utm_campaign : '',
                'UTM_CONTENT' => isset($order->orderUtm) ? $order->orderUtm->utm_content : '',
                'UTM_TERM' => isset($order->orderUtm) ? $order->orderUtm->utm_term : '',
            ],
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ];
    }

    private function prepareFeedback(Feedback $feedback): array
    {
        return [
            'fields' => [
                'TITLE' => '№'.$feedback->id.' - обратная связь с сайта '.env('APP_URL'),
                'NAME' => $feedback->name,
                'EMAIL' => [
                    ['VALUE' => $feedback->email, 'VALUE_TYPE' => 'WORK'],
                ],
                'PHONE' => [
                    ['VALUE' => $feedback->phone, 'VALUE_TYPE' => 'WORK'],
                ],
                'COMMENTS' => $feedback->comment,
                'DATE_CREATE' => $feedback->created_at,
            ],
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ];
    }

    private function prepareProducts($order): array
    {
        return $order->products->map(function ($product) {
            return [
                'PRODUCT_ID' => $product->product_id,
                'PRICE' => $product->price,
                'QUANTITY' => $product->count,
            ];
        })->toArray();
    }

    private function getProducts($order): array
    {
        return $order->products->map(function ($product) {
            return $product->product->t('title').', кол. - '.$product->count.', цена - '.$product->price.'грн.';
        })->toArray();
    }
}

<?php

namespace App\Services\Payment\Gateways\EasyPay;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EasyPayClient
{
    private const API_URL = 'https://merchantapi.easypay.ua';

    public function __construct(
        private readonly string $partnerKey,
        private readonly string $serviceKey,
        private readonly string $secretKey,
    ) {}

    /**
     * Крок 1 — реєстрація застосунку, повертає appId + pageId.
     */
    public function createApp(): ?object
    {
        $response = Http::withHeaders([
            'PartnerKey' => $this->partnerKey,
            'locale' => 'ua',
        ])->timeout(10)->post(self::API_URL.'/api/system/createApp');

        if (! $response->successful()) {
            Log::channel('payments')->warning('EasyPay: createApp error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->object();
    }

    /**
     * Крок 2 — створення замовлення, повертає forwardUrl для редіректу.
     */
    public function createOrder(string $appId, string $pageId, string $body): ?object
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'PartnerKey' => $this->partnerKey,
            'locale' => 'ua',
            'AppId' => $appId,
            'PageId' => $pageId,
            'Sign' => $this->sign($body),
        ])->timeout(10)->withBody($body, 'application/json')
            ->post(self::API_URL.'/api/merchant/createOrder');

        if (! $response->successful()) {
            Log::channel('payments')->warning('EasyPay: createOrder error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->object();
    }

    /**
     * Отримує статус замовлення за orderId.
     */
    public function getOrder(string $orderId): ?object
    {
        $body = (string) json_encode(['orderId' => $orderId]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'PartnerKey' => $this->partnerKey,
            'Sign' => $this->sign($body),
        ])->timeout(10)->withBody($body, 'application/json')
            ->post(self::API_URL.'/api/merchant/getOrder');

        if (! $response->successful()) {
            return null;
        }

        return $response->object();
    }

    /**
     * Будує JSON-тіло для createOrder.
     */
    public function buildBody(Order $order): string
    {
        $amount = $order->price_delivery
            ? $order->price_delivery + $order->cost
            : $order->cost;

        $phone = preg_replace('/[^A-Za-z0-9]/', '', $order->phone ?? '');

        return (string) json_encode([
            'userInfo' => [
                'phone' => $phone,
            ],
            'order' => [
                'serviceKey' => $this->serviceKey,
                'orderId' => (string) $order->id,
                'description' => __t('Нове замовлення').' #'.$order->id,
                'amount' => $amount,
                'additionalItems' => [
                    'PayerEmail' => $order->email,
                    'PayerPhone' => $phone,
                    'Merchant.UrlNotify' => url('/payment/webhook/easypay'),
                ],
            ],
            'urls' => [
                'success' => route('checkout.success', $order),
                'failed' => url('/payment/failed'),
            ],
        ]);
    }

    /**
     * Підпис: base64(sha256(secret_key + body)).
     */
    public function sign(string $body): string
    {
        return base64_encode(hash('sha256', $this->secretKey.$body, true));
    }
}

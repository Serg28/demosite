<?php

namespace App\Services\Checkouts\EasyPay;

use App\Models\Order;
use Log;

class EasyPay
{
    private Order $order;

    private array $header;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->header = [
            'PartnerKey: '.config('services.easypay.partner_key'),
            'locale: ua',
        ];
    }

    public function init()
    {
        $registration = $this->registration();

        if (! $registration || $registration->error != null) {
            return redirect()->back()->withErrors(['message' => __t('Помила при реєстрації')]);
        }

        $orderCreate = $this->createOrder($registration);

        if ($orderCreate->error) {
            Log::error('Order Pay not confirmed.');

            return redirect()->back()->withErrors(['message' => __t('Помилка при створенні платежа')]);
        }

        return redirect($orderCreate->forwardUrl);
    }

    public function registration()
    {
        $header = [
            'PartnerKey: '.config('services.easypay.partner_key'),
            'locale: ua',
        ];

        $url = 'https://merchantapi.easypay.ua/api/system/createApp';

        return $this->send($url, $header);
    }

    public function createOrder($registration): mixed
    {
        $body = $this->getBody();

        $sign = base64_encode(hash('sha256', (config('services.easypay.secret_key').$body), true));

        $header = [
            'Content-Type: application/json',
            'PartnerKey: '.config('services.easypay.partner_key'),
            'locale: ua',
            'AppId:'.$registration->appId,
            'PageId:'.$registration->pageId,
            'Sign:'.$sign,
        ];

        $url = 'https://merchantapi.easypay.ua/api/merchant/createOrder';

        return $this->send($url, $header, $body);
    }

    public function send(string $url, string $header, string $body = ''): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        curl_close($ch);

        return $this->decode($response);
    }

    public function getBody(): string
    {
        $price = $this->order->price_delivery ?
            $this->order->price_delivery + $this->order->cost :
            $this->order->cost;

        return $this->encode([
            'userInfo' => [
                'phone' => $this->order->clearPhone(),
            ],
            'order' => [
                'serviceKey' => config('services.easypay.service_key'),
                'orderId' => $this->order->id,
                'description' => __t('Нове замовлення'),
                'amount' => $price,
                'additionalItems' => [
                    'PayerEmail' => $this->order->email,
                    'PayerPhone' => $this->order->clearPhone(),
                    'Merchant.UrlNotify' => geturl('/easypay/success'),
                ],
            ],
            'urls' => [
                'success' => geturl('/easypay/success'),
                'failed' => geturl('/easypay/error'),
            ],
        ]);
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function encode(array $data): string
    {
        return json_encode($data);
    }

    private function decode(string $data): mixed
    {
        return json_decode($data);
    }
}

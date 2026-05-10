<?php

namespace App\Services\Payment\Gateways\PayLink;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayLinkClient
{
    private const CURRENCY_CODE = '980'; // UAH

    private const ORDER_TYPE = '3';

    public function __construct(
        private readonly string $merchantId,
        private readonly string $privateKey,
        private readonly string $apiUrl,
        private readonly string $frameUrl,
    ) {}

    /**
     * Створює платіжну сесію (type=getSession).
     * Повертає масив з полем sid або null при помилці.
     *
     * @param  array<string, mixed>  $params  tranid, amount, description, callback, clientParams
     * @return array<string, mixed>|null
     */
    public function getSession(array $params): ?array
    {
        $data = array_merge([
            'type' => 'getSession',
            'merchant' => $this->merchantId,
            'currency' => self::CURRENCY_CODE,
            'ordertype' => self::ORDER_TYPE,
            'is_3DS' => 'N',
        ], $params);

        $data['amount'] = number_format((float) $data['amount'], 2, '.', '');
        $data['sign'] = $this->generateSign($data);

        $response = Http::timeout(10)->post($this->apiUrl, $data);

        if (! $response->successful()) {
            Log::channel('payments')->warning('PayLink: getSession error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Перевіряє статус транзакції (type=getTranState).
     * Повертає масив з полями resultCode та oper_data або null.
     *
     * @return array<string, mixed>|null
     */
    public function getTranState(string $sid): ?array
    {
        $data = [
            'type' => 'getTranState',
            'sid' => $sid,
        ];

        $data['sign'] = $this->generateSign($data);

        $response = Http::timeout(10)->post($this->apiUrl, $data);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Формує URL для widget/iframe оплати.
     */
    public function getPaymentFrameUrl(string $sid): string
    {
        return $this->frameUrl.'/frame/pay/?sid='.$sid;
    }

    /**
     * Підпис: base64(hmac_sha1(privateKey + base64(json(data)) + privateKey, privateKey)).
     *
     * @param  array<string, mixed>  $data
     */
    public function generateSign(array $data): string
    {
        $jsonData = (string) json_encode($data, JSON_UNESCAPED_UNICODE);
        $base64Data = base64_encode($jsonData);
        $concat = $this->privateKey.$base64Data.$this->privateKey;

        return base64_encode(hash_hmac('sha1', $concat, $this->privateKey, true));
    }
}

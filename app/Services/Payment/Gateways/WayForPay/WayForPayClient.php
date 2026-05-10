<?php

namespace App\Services\Payment\Gateways\WayForPay;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WayForPayClient
{
    private const CHECKOUT_URL = 'https://secure.wayforpay.com/pay';

    private const API_URL = 'https://api.wayforpay.com/api';

    public function __construct(
        private readonly string $merchantAccount,
        private readonly string $merchantSecret,
        private readonly string $merchantDomain,
    ) {}

    /**
     * Будує дані форми для POST-редіректу на WayForPay.
     *
     * @param  array<string, mixed>  $params  productName, productCount, productPrice — масиви
     * @return array<string, mixed>
     */
    public function buildFormData(array $params): array
    {
        $orderReference = (string) $params['orderReference'];
        $orderDate = (int) ($params['orderDate'] ?? time());
        $amount = (float) $params['amount'];
        $currency = $params['currency'] ?? 'UAH';
        $productNames = (array) $params['productName'];
        $productCounts = (array) $params['productCount'];
        $productPrices = (array) $params['productPrice'];

        return array_merge($params, [
            'merchantAccount' => $this->merchantAccount,
            'merchantDomain' => $this->merchantDomain,
            'merchantSignature' => $this->signForm(
                $orderReference, $orderDate, $amount, $currency,
                $productNames, $productCounts, $productPrices,
            ),
            'checkout_url' => self::CHECKOUT_URL,
        ]);
    }

    /**
     * Перевіряє статус транзакції (transactionType: CHECK_STATUS).
     */
    public function checkStatus(string $orderReference): ?object
    {
        return $this->post([
            'transactionType' => 'CHECK_STATUS',
            'merchantAccount' => $this->merchantAccount,
            'orderReference' => $orderReference,
            'merchantSignature' => $this->apiSign($this->merchantAccount, $orderReference),
        ]);
    }

    /**
     * Підтверджує захоплення заблокованих коштів (CAPTURE).
     */
    public function capture(string $orderReference, float $amount, string $currency = 'UAH'): ?object
    {
        return $this->post([
            'transactionType' => 'CAPTURE',
            'merchantAccount' => $this->merchantAccount,
            'orderReference' => $orderReference,
            'amount' => $this->formatAmount($amount),
            'currency' => $currency,
            'merchantSignature' => $this->apiSign(
                $this->merchantAccount, $orderReference, $this->formatAmount($amount), $currency,
            ),
        ]);
    }

    /**
     * Скасовує холд (VOID).
     */
    public function void(string $orderReference): ?object
    {
        return $this->post([
            'transactionType' => 'VOID',
            'merchantAccount' => $this->merchantAccount,
            'orderReference' => $orderReference,
            'merchantSignature' => $this->apiSign($this->merchantAccount, $orderReference),
        ]);
    }

    /**
     * Повертає кошти (REFUND).
     */
    public function refund(string $orderReference, float $amount, string $currency = 'UAH', string $comment = 'Refund'): ?object
    {
        return $this->post([
            'transactionType' => 'REFUND',
            'merchantAccount' => $this->merchantAccount,
            'orderReference' => $orderReference,
            'amount' => $this->formatAmount($amount),
            'currency' => $currency,
            'comment' => $comment,
            'merchantSignature' => $this->apiSign(
                $this->merchantAccount, $orderReference, $this->formatAmount($amount), $currency, $comment,
            ),
        ]);
    }

    /**
     * Верифікує HMAC-MD5 підпис webhook-а від WayForPay.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        $expected = $this->apiSign(
            $payload['merchantAccount'] ?? '',
            $payload['orderReference'] ?? '',
            $this->formatAmount((float) ($payload['amount'] ?? 0)),
            $payload['currency'] ?? '',
            $payload['authCode'] ?? '',
            $payload['cardPan'] ?? '',
            $payload['transactionStatus'] ?? '',
            (string) ($payload['reasonCode'] ?? ''),
        );

        return hash_equals($expected, $payload['merchantSignature'] ?? '');
    }

    /**
     * Будує обов'язкову відповідь на webhook WayForPay (status: accept).
     *
     * @return array<string, mixed>
     */
    public function buildWebhookResponse(string $orderReference): array
    {
        $time = time();

        return [
            'orderReference' => $orderReference,
            'status' => 'accept',
            'time' => $time,
            'signature' => $this->apiSign($this->merchantAccount, $orderReference, (string) $time),
        ];
    }

    /**
     * Підпис форми: HMAC-MD5 по полях merchantAccount...productPrice[].
     *
     * @param  array<int|float|string>  $productNames
     * @param  array<int|float|string>  $productCounts
     * @param  array<int|float|string>  $productPrices
     */
    private function signForm(
        string $orderReference,
        int $orderDate,
        float $amount,
        string $currency,
        array $productNames,
        array $productCounts,
        array $productPrices,
    ): string {
        $parts = array_merge(
            [$this->merchantAccount, $this->merchantDomain, $orderReference, $orderDate, $this->formatAmount($amount), $currency],
            $productNames,
            $productCounts,
            array_map(fn ($p) => $this->formatAmount((float) $p), $productPrices),
        );

        return hash_hmac('md5', implode(';', $parts), $this->merchantSecret);
    }

    private function apiSign(string ...$parts): string
    {
        return hash_hmac('md5', implode(';', $parts), $this->merchantSecret);
    }

    /** @param  array<string, mixed>  $data */
    private function post(array $data): ?object
    {
        $response = Http::timeout(10)
            ->post(self::API_URL, $data);

        if (! $response->successful()) {
            Log::channel('payments')->warning('WayForPay API error', [
                'transactionType' => $data['transactionType'] ?? null,
                'httpStatus' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->object();
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}

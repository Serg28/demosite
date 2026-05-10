<?php

namespace App\Services\Payment\Gateways\Privat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrivatPayPartsClient
{
    private const BASE_URL = 'https://payparts2.privatbank.ua/ipp/v2';

    public function __construct(
        private readonly string $storeId,
        private readonly string $password,
    ) {}

    /**
     * Створює платіж. URL залежить від типу: /payment/hold або /payment/create.
     * Підпис: base64(sha1(pwd+storeId+orderId+amountKopecks+currency+partsCount+merchantType+responseUrl+redirectUrl+productsStr+pwd)).
     *
     * @param  array<int, array{name: string, count: int, price: float}>  $products
     * @return array{token: string, paymentUrl: string}|null
     */
    public function createPayment(
        string $orderId,
        float $amount,
        string $redirectUrl,
        string $responseUrl,
        int $partsCount = 0,
        array $products = [],
        string $merchantType = 'PP',
        bool $useHold = false,
    ): ?array {
        if (empty($products)) {
            $products = [['name' => 'Order', 'count' => 1, 'price' => $amount]];
        }

        $productsString = $this->buildProductsString($products);
        $amountKopecks  = (string) (int) round($amount * 100);
        $currency       = '980';

        $signature = $this->sign(
            $this->password, $this->storeId,
            $orderId, $amountKopecks, $currency,
            (string) $partsCount, $merchantType,
            $responseUrl, $redirectUrl,
            $productsString,
            $this->password,
        );

        $url = $useHold
            ? self::BASE_URL . '/payment/hold'
            : self::BASE_URL . '/payment/create';

        $response = $this->post($url, [
            'storeId'      => $this->storeId,
            'orderId'      => $orderId,
            'amount'       => $amount,
            'partsCount'   => $partsCount,
            'merchantType' => $merchantType,
            'currency'     => $currency,
            'products'     => $products,
            'responseUrl'  => $responseUrl,
            'redirectUrl'  => $redirectUrl,
            'signature'    => $signature,
        ]);

        if ($response === null) {
            return null;
        }

        return [
            'token'      => $response->token ?? '',
            'paymentUrl' => $response->paymentUrl ?? '',
        ];
    }

    /**
     * Отримує статус замовлення через /payment/state.
     * Підпис: base64(sha1(pwd+storeId+orderId+pwd)).
     */
    public function checkStatus(string $orderId): ?object
    {
        $signature = $this->sign($this->password, $this->storeId, $orderId, $this->password);

        return $this->post(self::BASE_URL . '/payment/state', [
            'storeId'    => $this->storeId,
            'orderId'    => $orderId,
            'showRefund' => 'false',
            'showAmount' => 'true',
            'signature'  => $signature,
        ]);
    }

    /**
     * Підтверджує (capture) платіж у стані LOCKED.
     * Підпис: base64(sha1(pwd+storeId+orderId+pwd)).
     */
    public function confirm(string $orderId): bool
    {
        $signature = $this->sign($this->password, $this->storeId, $orderId, $this->password);

        $response = $this->post(self::BASE_URL . '/payment/confirm', [
            'storeId'   => $this->storeId,
            'orderId'   => $orderId,
            'signature' => $signature,
        ]);

        return $response !== null && ($response->state ?? '') === 'SUCCESS';
    }

    /**
     * Скасовує платіж (MERCHANT_CANCEL).
     * Підпис: base64(sha1(pwd+storeId+orderId+pwd)).
     */
    public function cancel(string $orderId): bool
    {
        $signature = $this->sign($this->password, $this->storeId, $orderId, $this->password);

        $response = $this->post(self::BASE_URL . '/payment/cancel', [
            'storeId'   => $this->storeId,
            'orderId'   => $orderId,
            'signature' => $signature,
        ]);

        return $response !== null;
    }

    /**
     * Повернення коштів після успішного списання (POST /payment/decline).
     * Підпис: base64(sha1(pwd+storeId+orderId+amountKopecks+pwd)).
     */
    public function decline(string $orderId, float $amount): bool
    {
        $amountKopecks = (string) (int) round($amount * 100);
        $signature     = $this->sign($this->password, $this->storeId, $orderId, $amountKopecks, $this->password);

        $response = $this->post(self::BASE_URL . '/payment/decline', [
            'storeId'   => $this->storeId,
            'orderId'   => $orderId,
            'amount'    => $amount,
            'signature' => $signature,
        ]);

        return $response !== null;
    }

    /**
     * Верифікує SHA1-Base64 підпис webhook-а від PrivatBank.
     * Підпис: base64(sha1(pwd+storeId+orderId+paymentState+message+pwd)).
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        $expected = $this->sign(
            $this->password,
            $this->storeId,
            (string) ($payload['orderId'] ?? ''),
            (string) ($payload['paymentState'] ?? ''),
            (string) ($payload['message'] ?? ''),
            $this->password,
        );

        return hash_equals($expected, $payload['signature'] ?? '');
    }

    /**
     * Будує рядок продуктів для підпису: name+count+priceKopecks для кожного.
     *
     * @param  array<int, array{name: string, count: int, price: float}>  $products
     */
    public function buildProductsString(array $products): string
    {
        $result = '';
        foreach ($products as $p) {
            $result .= $p['name'] . $p['count'] . (int) round($p['price'] * 100);
        }

        return $result;
    }

    // ─── HTTP ────────────────────────────────────────────────────

    /** @param  array<string, mixed>  $data */
    private function post(string $url, array $data): ?object
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->post($url, $data);

        if (! $response->successful()) {
            Log::channel('payments')->warning('PrivatPayParts API error', [
                'url'        => $url,
                'httpStatus' => $response->status(),
                'body'       => $response->body(),
            ]);

            return null;
        }

        return $response->object();
    }

    private function sign(string ...$parts): string
    {
        return base64_encode(sha1(implode('', $parts), true));
    }
}

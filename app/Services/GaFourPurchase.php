<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class GaFourPurchase
{
    private $order;
    private $measurementId;
    private $api_secret;
    private $currency;
    protected $cookieGA;

    public $config = [];

    public function __construct(Order $order, $cookieGA)
    {
        $this->measurementId = setting('measurement-id-universal-google-analytic-4');
        $this->api_secret = setting('api-secret-universal-google-analytic-4');
        $this->currency = 'UAH';
        $this->order = $order;
        $this->cookieGA = $cookieGA;
        Log::info('GA4 Purchase cookie: '.$cookieGA);
    }

    private function getProducts()
    {
        return $this->order->cartOrderProducts->map(function ($item) {
            return [
                'item_name' => $item->t('title'),
                'item_id' => $item->getArticle(),
                'item_brand' => $item->getBrandName(),
                'item_category' => $item->getTopCategoryName(),
                'item_category2' => $item->getParentCategoryName(),
                'quantity' => $item->pivot->count,
                'item_price' => $item->pivot->price,
                'price' => $item->pivot->price,
                'tax' => 0
            ];
        });
    }

    /*private function generate_cid()
    {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); //set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); //set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }*/ // end generate_cid()

    public function getUserId()
    {
        if (!empty($this->config['user_id'])) {
            return $this->config['user_id'];
        }

        $cid = '';
        if (!empty($this->cookieGA)) {
            $cid = preg_replace('/^[^\.]+\.[^\.]+\./ui', '', $this->cookieGA);
        }
        if (empty($cid)) {
            $cid = session_id() ?: $this->genUniqUserId();
        }
        $this->config['user_id'] = $cid;

        return $this->config['user_id'];
    }

    /*
     * Генерирует уникальный ID пользователя
     *
     * @return string
     */
    private function genUniqUserId()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /*
     * Получаем session id
     *
     * @return ?string
     */
    private function getSessionNumber(): ?string
    {
        $streamIDParts = explode('-', $this->measurementId);
        if (count($streamIDParts) > 1) {
            $streamID = $streamIDParts[1];
        } else {
            return null;
        }

        //$pattern = '/_ga_'.$streamID.'=GS1\.1\.(\d+)\.\d+\.\d+\.\d+\.\d+\.\d+\.\d+/';
        $pattern = '/_ga_'.$streamID.'=(GS\d\.\d)\.(\d+)\.\d+\.\d+\.\d+\.\d+\.\d+\.\d+/';

        $attempts = 2; // Ограничение на количество попыток
        for ($i = 0; $i < $attempts; $i++) {
            if (isset($_SERVER['HTTP_COOKIE']) && is_string($_SERVER['HTTP_COOKIE'])) {
                if (preg_match($pattern, $_SERVER['HTTP_COOKIE'], $matches)) {
                    $gaVersion = $matches[1];
                    $sessionNumber = $matches[2];

                    // Проверка версии GA Stream ID
                    if ($gaVersion === 'GS1.1' || $gaVersion === 'GS2.2') {
                        return $sessionNumber;
                    }
                }
            }
            usleep(100000); // Подождать 100 миллисекунд (0.1 секунды) перед следующей попыткой
        }

        return null; // Если сессия так и не найдена, вернуть null
    }

    public function send()
    {
        $transactionData = [
            'client_id' => $this->getUserId(),
            'events' => [
                'name' => 'purchase',
                'params' => [
                    'engagement_time_msec' => '100',
                    'session_id' => $this->getSessionNumber(),
                    'transaction_id' => (string)$this->order->id,
                    'affiliation' => $this->getUserType(),
                    //Передаем сумму заказа с доставкой и скидками
                    'value' => $this->order->getPriceForDocumentsAttribute(),
                    //Если доставка бесплатная передаем значения 0, если платная передаем всем 100.00 вне зависимости от ее суммы.
                    'shipping' => ($this->order->price_delivery > 0) ? 100.00 : 0,
                    'tax' => 0,
                    'currency' => $this->currency,
                    //Передаем код введенного промокода, если промокод не введен оставляем поле пустым.
                    'coupon' => $this->order->promo_code ?: '',
                    'payment_type' => $this->order->paymentName(),
                    'purchase_type' => $this->getPurchaseType(),
                    'items' => $this->getProducts()
                ]
            ]
        ];
        return $this->sendData($transactionData);
    }

    // "Card owner" - есть дисконтная карта,
    // “Login user” - если пользователь залогинен, но не регистрировал дисконтную карту,
    // “New user” - если пользователь новый в нашей базе.
    private function getUserType(): string
    {
        if ($this->order->user === null) {
            return 'Login user';
        }

        if ($this->order->user->discountcard) {
            return 'Card owner';
        }

        if (is_object($this->order->user) && method_exists(
            $this->order->user,
            'isNew'
        ) && $this->order->user->isNew()) {
            return 'New user';
        }

        return 'Login user';
    }

    private function getPurchaseType(): string
    {
        return $this->order->is_quick ? '1 click' : 'Standard';
    }

    private function sendData($data)
    {
        $payload = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        //$payload =  $data;
        $ch = curl_init('https://www.google-analytics.com/mp/collect?measurement_id=' . $this->measurementId . '&api_secret=' . $this->api_secret);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        //----------------------
        //debug
        //if ($this->debug) {
        $ch = curl_init('https://www.google-analytics.com/debug/mp/collect?measurement_id=' . $this->measurementId . '&api_secret=' . $this->api_secret);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        Log::info('GA4 Purchase: '.print_r([
                //'order_id' => $this->order->get('num'),
                'data' => json_decode($payload, true),
                'response' => $response,
                'httpcode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                //'cookie' => preg_replace('/^[^\.]+\.[^\.]+\./ui', '', $_COOKIE['_ga'])
            ], true));

        curl_close($ch);
        //}
        //---



        return $httpcode;
    }
}

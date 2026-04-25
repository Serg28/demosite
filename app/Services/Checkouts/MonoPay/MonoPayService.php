<?php

namespace App\Services\Checkouts\MonoPay;

use GuzzleHttp\Exception\GuzzleException;
use MonoPay\Client;
use MonoPay\Payment;

/**
 * Класс MonoPayService отвечает за взаимодействие с MonoPay API.
 */
class MonoPayService
{
    /**
     * @var Client $client Экземпляр клиента для взаимодействия с MonoPay API.
     */
    protected Client $client;

    /**
     * @var Payment $payment Экземпляр для создания платежей через MonoPay API.
     */
    protected Payment $payment;

    /**
     * Конструктор класса MonoPayService.
     *
     * Инициализирует клиента и объект для создания платежей.
     *
     * @throws GuzzleException Исключение, которое может быть выброшено при запросах через Guzzle.
     */
    public function __construct()
    {
        // Создаем клиента для отправки запросов
        $token = setting('monobank-monopay-token') ?: config('services.mono_pay.token');
        $custom_headers = [
            'X-Cms' => 'Linecore'
        ];
        $this->client = new Client($token, $custom_headers);

        // Для создания платежей создаем объект Payment
        $this->payment = new Payment($this->client);
    }

    /**
     * Возвращает идентификатор мерчанта.
     *
     * @return string Идентификатор мерчанта.
     */
    public function getMerchantId(): string
    {
        return $this->client->getMerchantId();
    }

    /**
     * Возвращает название мерчанта.
     *
     * @return string Название мерчанта.
     */
    public function getMerchantName(): string
    {
        return $this->client->getMerchantName();
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}

<?php

namespace App\Services\Checkouts\WayForPay;

use Maksa988\WayForPay\Domain\Client;

class WayForPayService
{
    /**
     * @var Client $client Экземпляр клиента для взаимодействия с WayForPay API.
     */
    protected Client $client;

    public function __construct() {

    }
}
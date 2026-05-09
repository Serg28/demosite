<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Реєстр платіжних гейтвеїв
    |--------------------------------------------------------------------------
    | Додати новий гейтвей = 1 клас + 1 рядок тут
    */
    'gateways' => [
        'liqpay'           => \App\Services\Payment\Gateways\LiqPay\LiqPayGateway::class,
        'liqpay_cod'       => \App\Services\Payment\Gateways\LiqPay\LiqPayCodGateway::class,
        'monopay'          => \App\Services\Payment\Gateways\MonoPay\MonoPayGateway::class,
        'monopayparts'     => \App\Services\Payment\Gateways\MonoPay\MonoPayPartsGateway::class,
        'wayforpay'        => \App\Services\Payment\Gateways\WayForPay\WayForPayGateway::class,
        'privatpayparts'   => \App\Services\Payment\Gateways\Privat\PrivatPayPartsGateway::class,
        'easypay'          => \App\Services\Payment\Gateways\EasyPay\EasyPayGateway::class,
        'novapay'          => \App\Services\Payment\Gateways\NovaPay\NovaPayGateway::class,
        'paylink'          => \App\Services\Payment\Gateways\PayLink\PayLinkGateway::class,
        'platon'           => \App\Services\Payment\Gateways\Platon\PlatonGateway::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Стратегії комісій для гейтвеїв
    |--------------------------------------------------------------------------
    */
    'commission_strategies' => [
        'monopayparts'   => \App\Services\Payment\Strategies\MonoPartsCommissionStrategy::class,
        'privatpayparts' => \App\Services\Payment\Strategies\PrivatPPCommissionStrategy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Дефолтна стратегія (flat відсоток)
    |--------------------------------------------------------------------------
    */
    'default_commission_strategy' => \App\Services\Payment\Strategies\FlatCommissionStrategy::class,

    /*
    |--------------------------------------------------------------------------
    | Ротація ключів — таблиця payment_credentials
    |--------------------------------------------------------------------------
    | day_of_week: 1=Пн ... 7=Нд (ISO 8601)
    */
    'credential_rotation' => true,
];

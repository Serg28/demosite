<?php

use App\PipelineSteps\Checkout\ApplyDiscounts;
use App\PipelineSteps\Checkout\CalculateCommission;
use App\PipelineSteps\Checkout\CalculateDelivery;
use App\PipelineSteps\Checkout\CalculateTotals;
use App\PipelineSteps\Checkout\ClearCart;
use App\PipelineSteps\Checkout\CreateOrder;
use App\PipelineSteps\Checkout\ProcessPayment;
use App\PipelineSteps\Checkout\SendNotifications;
use App\PipelineSteps\Checkout\ValidateCart;
use App\PipelineSteps\Checkout\ValidateGiftCertificates;

return [
    /*
    |--------------------------------------------------------------------------
    | Pipeline кроки Checkout
    |--------------------------------------------------------------------------
    | Порядок важливий. Кожен крок реалізує handle(CheckoutContext, Closure).
    */
    'pipeline' => [
        ValidateCart::class,
        ApplyDiscounts::class,
        ValidateGiftCertificates::class,
        CalculateDelivery::class,
        CalculateCommission::class,
        CalculateTotals::class,
        CreateOrder::class,
        ProcessPayment::class,
        ClearCart::class,
        SendNotifications::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Режим знижок
    |--------------------------------------------------------------------------
    | per_product — знижка розподілена по товарах (для 1С-сумісності)
    | order_level — знижка = окремий рядок у підсумку
    */
    'discount_mode' => env('CHECKOUT_DISCOUNT_MODE', 'per_product'),

    /*
    |--------------------------------------------------------------------------
    | Режим податку (НДС)
    |--------------------------------------------------------------------------
    | per_product — НДС у CartItem (пакет linecore/shoppingcart)
    | order_level — НДС = окремий рядок, taxRate=0 у CartItem
    */
    'tax_mode' => env('CHECKOUT_TAX_MODE', 'per_product'),

    /*
    |--------------------------------------------------------------------------
    | Сумісність знижок
    |--------------------------------------------------------------------------
    | true  = можна комбінувати, false = взаємовиключають
    */
    'discount_combinations' => [
        'promo_code' => ['discount_card' => true],
        'discount_card' => ['promo_code' => true],
    ],

    /*
    |--------------------------------------------------------------------------
    | Резервування подарункових сертифікатів
    |--------------------------------------------------------------------------
    | TTL резерву в Cache (секунди), ключ gift:reserve:{code}
    */
    'gift_certificate_reserve_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Стратегія сгорання сертифікатів
    |--------------------------------------------------------------------------
    | burn_all     — залишок балансу сгорає (default)
    | keep_remaining — залишок залишається на сертифікаті
    */
    'gift_certificate_burn_strategy' => env('GIFT_CERTIFICATE_BURN_STRATEGY', 'burn_all'),

    /*
    |--------------------------------------------------------------------------
    | Правила підформ доставки (ключ = slug доставки)
    |--------------------------------------------------------------------------
    | required — поля, що блокують перехід далі та валідуються у placeOrder().
    | optional — поля, що зберігаються але не блокують.
    | Нова доставка — просто додати запис, без змін у PHP.
    */
    'delivery_fields' => [
        'np_branch'    => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'np_poshtamat' => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'ukrposhta'    => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'meest'        => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'justin'       => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'rozetka'      => ['required' => ['deliveryWarehouseId'], 'optional' => []],
        'courier'      => ['required' => ['street', 'house'], 'optional' => ['apartment', 'building', 'floor']],
        'pickup'       => ['required' => ['deliveryPickupPointId'], 'optional' => []],
        // Доставки без підформи — просто не додавати запис
    ],

    /*
    |--------------------------------------------------------------------------
    | Правила підформ оплати (ключ = gateway slug)
    |--------------------------------------------------------------------------
    | Визначає поля для кожного платіжного шлюзу.
    | paymentStepValid та placeOrder() беруть правила звідси, не з коду.
    | Новий gateway — просто додати запис.
    */
    'payment_fields' => [
        'monopayparts'   => ['payPartsCount' => ['required', 'integer', 'min:2', 'max:12']],
        'privatpayparts' => ['payPartsCount' => ['required', 'integer', 'min:2', 'max:10']],
        'paylink'        => [
            'b2bCompany' => ['required', 'string', 'max:200'],
            'b2bEdrpou'  => ['required', 'digits:8'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Максимальна кількість місяців розстрочки (для UI-селекту)
    |--------------------------------------------------------------------------
    */
    'payment_parts_months' => [
        'monopayparts'   => 12,
        'privatpayparts' => 10,
    ],
];

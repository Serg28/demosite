<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum DeliveryMethodEnum: int
{
    use ToArrayTrait;

    case CourierSelf = 1; // Нашим курьером
    case NovaPoshtaAddress = 2; // Новою поштою (адресна доставка)
    case NovaPoshtaPickup = 3; // Новою Поштою (у відділення)
    case SelfPickup = 4; // Самовивіз
    case NovaPoshtaPostomat = 5; // Новою Поштою (поштомат)


    case CourierInKyiv = 7; // Кур'єром по Києву
    case Ukrposhta = 6; // Укрпошта


    public static function SelfPickup(): int
    {
        return self::SelfPickup->value;
    }

    public static function NovaPoshtaPickup(): int
    {
        return self::NovaPoshtaPickup->value;
    }

    public static function NovaPoshtaPostomat(): int
    {
        return self::NovaPoshtaPostomat->value;
    }

    public static function CourierInKyiv(): int
    {
        return self::CourierInKyiv->value;
    }

    public static function CourierSelf(): int
    {
        return self::CourierSelf->value;
    }

    public static function Courier(): int
    {
        return self::CourierInKyiv->value;
    }

    public static function Ukrposhta(): int
    {
        return self::Ukrposhta->value;
    }

    public static function NovaPoshtaAddress(): int
    {
        return self::NovaPoshtaAddress->value;
    }
}

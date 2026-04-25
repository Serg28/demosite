<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum AssemblyStatusEnum: int
{
    use ToArrayTrait;

    case NoStatus = 0; // Без статуса
    case AwaitingAssembly = 1; // Очікує комплектацію
    case OrderedFromSupplier = 2; // Замовлено у постачальника
    case BeingAssembled = 3; // Комплектується
    case Assembled = 4; // Укомплектоване
    case Packed = 5; // Спаковане
    case ReadyForPickup = 6; // Готовий до видачі

    public static function NoStatus(): int
    {
        return self::NoStatus->value;
    }

    public static function AwaitingAssembly(): int
    {
        return self::AwaitingAssembly->value;
    }

    public static function OrderedFromSupplier(): int
    {
        return self::OrderedFromSupplier->value;
    }

    public static function BeingAssembled(): int
    {
        return self::BeingAssembled->value;
    }

    public static function Assembled(): int
    {
        return self::Assembled->value;
    }

    public static function Packed(): int
    {
        return self::Packed->value;
    }

    public static function ReadyForPickup(): int
    {
        return self::ReadyForPickup->value;
    }
}

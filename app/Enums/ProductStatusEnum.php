<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum ProductStatusEnum: int
{
    use ToArrayTrait;

    case InStock = 5; // В наявності
    case OutOfStock = 6; // Немає в наявності
    case Expected = 7; // Очікується
    case Order5To7Days = 8; // Під замовлення 5 - 7 робочих днів
    case Order7To10Days = 9; // Під замовлення 7 - 10 робочих днів
    case Order10To14Days = 10; // Під замовлення 10 - 14 робочих днів
    case Available = 11; // В доступі

    public static function InStock(): int
    {
        return self::InStock->value;
    }

    public static function OutOfStock(): int
    {
        return self::OutOfStock->value;
    }

    public static function Expected(): int
    {
        return self::Expected->value;
    }

    public static function Order5To7Days(): int
    {
        return self::Order5To7Days->value;
    }

    public static function Order7To10Days(): int
    {
        return self::Order7To10Days->value;
    }

    public static function Order10To14Days(): int
    {
        return self::Order10To14Days->value;
    }

    public static function Available(): int
    {
        return self::Available->value;
    }
}

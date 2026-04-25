<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum PaymentStatusEnum: int
{
    use ToArrayTrait;

    case Unpaid = 0; // Несплачений
    case Paid = 1; // Сплачений
    case Blocked = 2; // Оплата блокирована
    case Awaiting = 3; // Очікуємо на оплату

    public static function Unpaid(): int
    {
        return self::Unpaid->value;
    }

    public static function Paid(): int
    {
        return self::Paid->value;
    }

    public static function Blocked(): int
    {
        return self::Blocked->value;
    }

    public static function Awaiting(): int
    {
        return self::Awaiting->value;
    }
}

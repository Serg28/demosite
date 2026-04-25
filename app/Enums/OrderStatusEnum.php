<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum OrderStatusEnum: int
{
    use ToArrayTrait;

    case New = 1; //Новий
    case InProcessing = 2; //Обробляється
    case ReadyForShipment = 3; //Готовий до відправки
    case SelfPickup = 4; //Самовивіз
    case Shipped = 5; //Відправлений
    case Delivered = 6; //Доставлений
    case Completed = 7; //Виконаний
    case RefusedToReceive = 8; //Відмова від отримання
    case Disbanded = 9; //Розформований
    case Canceled = 10; //Скасований
    case Return = 11; //Повернення
    case Paid = 12; //Оплачений

    public static function New(): int
    {
        return self::New->value;
    }

    public static function InProcessing(): int
    {
        return self::InProcessing->value;
    }

    public static function ReadyForShipment(): int
    {
        return self::ReadyForShipment->value;
    }

    public static function SelfPickup(): int
    {
        return self::SelfPickup->value;
    }

    public static function Shipped(): int
    {
        return self::Shipped->value;
    }

    public static function Delivered(): int
    {
        return self::Delivered->value;
    }

    public static function Completed(): int
    {
        return self::Completed->value;
    }

    public static function RefusedToReceive(): int
    {
        return self::RefusedToReceive->value;
    }

    public static function Disbanded(): int
    {
        return self::Disbanded->value;
    }

    public static function Canceled(): int
    {
        return self::Canceled->value;
    }

    public static function Return(): int
    {
        return self::Return->value;
    }

    public static function Paid(): int
    {
        return self::Paid->value;
    }
}

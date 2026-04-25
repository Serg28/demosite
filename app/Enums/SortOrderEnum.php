<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum SortOrderEnum: string
{

    use ToArrayTrait;

    case POPULARITY = 'popularity';
    case PRICE_UP = 'priceup';
    case PRICE_DOWN = 'pricedown';
    case DEFAULT = 'default';
    case DATE = 'date';
    case DATE_UP = 'dateup';
    case DATE_DOWN = 'datedown';

    public static function popularity(): string
    {
        return self::POPULARITY->value;
    }

    public static function priceUp(): string
    {
        return self::PRICE_UP->value;
    }

    public static function priceDown(): string
    {
        return self::PRICE_DOWN->value;
    }

    public static function default(): string
    {
        return self::DEFAULT->value;
    }

    public static function date(): string
    {
        return self::DATE->value;
    }

    public static function dateDown(): string
    {
        return self::DATE_DOWN->value;
    }

    public static function dateUp(): string
    {
        return self::DATE_UP->value;
    }
}

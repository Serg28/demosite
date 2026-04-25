<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum ShowCountEnum: int
{
    use ToArrayTrait;

    case SHOW_12 = 12;
    case SHOW_18 = 18;
    case SHOW_20 = 20;
    case SHOW_32 = 32;
    case SHOW_48 = 48;
    case SHOW_62 = 62;

    public static function show12(): int
    {
        return self::SHOW_12->value;
    }

    public static function show18(): int
    {
        return self::SHOW_18->value;
    }

    public static function show20(): int
    {
        return self::SHOW_20->value;
    }

    public static function show32(): int
    {
        return self::SHOW_32->value;
    }

    public static function show48(): int
    {
        return self::SHOW_48->value;
    }

    public static function show62(): int
    {
        return self::SHOW_62->value;
    }
}

<?php

namespace App\Enums\Traits;

trait ToArrayTrait
{
    public static function toArray(): array
    {
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }

        return $array;
    }

    public static function toValues(): array
    {
        return array_values(self::toArray());
    }
}

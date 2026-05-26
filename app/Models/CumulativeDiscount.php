<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ступені накопичувальної знижки.
 *
 * Поля:
 *   total_sum  — поріг накопиченої суми виконаних замовлень (грн)
 *   discount   — відсоток знижки (%)
 *
 * Логіка: беремо найбільший рівень, де total_sum <= суми виконаних замовлень.
 */
class CumulativeDiscount extends Model
{
    protected $fillable = ['total_sum', 'discount'];

    protected function casts(): array
    {
        return [
            'total_sum' => 'decimal:2',
            'discount'  => 'integer',
        ];
    }
}

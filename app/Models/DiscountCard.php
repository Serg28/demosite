<?php

namespace App\Models;

use App\Contracts\DiscountSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCard extends Model implements DiscountSource
{
    protected $table = 'discount_cards';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'float',
            'is_active'        => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getType(): string
    {
        return 'discount_card';
    }

    public function getAmount(float $subtotal): float
    {
        return round($subtotal * $this->discount_percent / 100, 2);
    }

    public function getLabel(): string
    {
        return __t('Дисконтна картка :number', ['number' => $this->number]);
    }

    public function isCompatibleWith(DiscountSource $other): bool
    {
        $combinations = config('checkout.discount_combinations', []);

        return (bool) ($combinations[$this->getType()][$other->getType()] ?? false);
    }
}

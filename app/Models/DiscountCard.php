<?php

namespace App\Models;

use App\Contracts\DiscountSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCard extends Model implements DiscountSource
{
    use HasFactory;

    protected $table = 'discount_cards';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value'                 => 'float',
            'is_active'             => 'boolean',
            'use_for_installments'  => 'boolean',
            'use_for_promotional'   => 'boolean',
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
        if ($this->type === 'percent') {
            return round($subtotal * $this->value / 100, 2);
        }

        return min($this->value, $subtotal);
    }

    public function getLabel(): string
    {
        return __t('Дисконтна картка :code', ['code' => $this->code]);
    }

    public function isCompatibleWith(DiscountSource $other): bool
    {
        if ($other->getType() === 'promo_code' && ! $this->use_for_promotional) {
            return false;
        }

        $combinations = config('checkout.discount_combinations', []);

        return (bool) ($combinations[$this->getType()][$other->getType()] ?? false);
    }
}

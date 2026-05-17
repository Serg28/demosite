<?php

namespace App\Models;

use App\Contracts\DiscountSource;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model implements DiscountSource
{
    protected $table = 'promo_codes';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value'                  => 'float',
            'min_order_amount'       => 'float',
            'expires_at'             => 'datetime',
            'is_active'              => 'boolean',
            'is_used'                => 'boolean',
            'use_for_installments'   => 'boolean',
            'use_for_promotional'    => 'boolean',
            'use_for_discount_cards' => 'boolean',
        ];
    }

    public function getType(): string
    {
        return 'promo_code';
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
        return __t('Промокод :code', ['code' => $this->code]);
    }

    public function isCompatibleWith(DiscountSource $other): bool
    {
        if ($other->getType() === 'discount_card' && ! $this->use_for_discount_cards) {
            return false;
        }

        $combinations = config('checkout.discount_combinations', []);

        return (bool) ($combinations[$this->getType()][$other->getType()] ?? false);
    }
}

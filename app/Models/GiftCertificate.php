<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCertificate extends Model
{
    protected $table = 'gift_certificates';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'                  => 'float',
            'is_active'               => 'boolean',
            'is_used'                 => 'boolean',
            'use_for_discount_cards'  => 'boolean',
            'use_for_promotional'     => 'boolean',
            'use_for_installments'    => 'boolean',
            'expires_at'              => 'datetime',
        ];
    }

    public function usedInOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'used_in_order_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', 1)
            ->where('is_used', 0)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}

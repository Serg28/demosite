<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayMethod extends Model
{
    use HasTranslations;

    protected $table = 'pay_methods';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'commission_percent' => 'float',
            'commission_rates' => 'array',
            'is_active' => 'boolean',
            'use_hold' => 'boolean',
        ];
    }

    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(
            Delivery::class,
            'delivery_payment',
            'payment_id',
            'delivery_id',
        );
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(PaymentCredential::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->orderBy('priority');
    }
}

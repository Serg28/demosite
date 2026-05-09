<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasTranslations;

    protected $table = 'deliveries';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'price'     => 'float',
            'free_cost' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            PayMethod::class,
            'delivery_payment',
            'delivery_id',
            'payment_id',
        );
    }

    public function points(): HasMany
    {
        return $this->hasMany(DeliveryPickupPoint::class)->orderBy('priority');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->orderBy('priority');
    }
}

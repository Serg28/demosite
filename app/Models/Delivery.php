<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
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
            'price' => 'float',
            'free_cost' => 'float',
            'is_active' => 'boolean',
            'is_for_all_cities' => 'boolean',
        ];
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'city_delivery');
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

    public function scopeForCity(Builder $query, ?int $cityId): Builder
    {
        if ($cityId === null) {
            return $query->where('is_for_all_cities', true);
        }

        return $query->where(function (Builder $q) use ($cityId) {
            $q->where('is_for_all_cities', true)
                ->orWhereHas('cities', fn (Builder $r) => $r->where('cities.id', $cityId));
        });
    }
}

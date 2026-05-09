<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasTranslations;

    protected $table = 'cities';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title'];

    public function warehouses(): HasMany
    {
        return $this->hasMany(DeliveryWarehouse::class);
    }

    public function carrierCodes(): HasMany
    {
        return $this->hasMany(CityCarrierCode::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}

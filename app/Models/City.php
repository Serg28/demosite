<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends BaseModel
{
    protected $fillable = [];

    public $timestamps = false;

    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(
            Delivery::class,
            'city_delivery',
            'city_id',
            'delivery_id'
        );
    }

    public function departments(): HasMany
    {
        return $this->hasMany(NPWarehouse::class, 'city_id');
    }

    public function novaposhtaWarehouse()
    {
        return $this->departments()->where('pochtomat','!=', 1);
    }

    public function novaposhtaPochtomat()
    {
        return $this->departments()->where('pochtomat', 1);
    }

    public function novaposhtaStreet()
    {
        return $this->hasMany(NPStreet::class, 'city_id');
    }

    public function ukrposhta(): HasMany
    {
        return $this->hasMany(UkrposhtaWarehouse::class, 'city_id');
    }

    public function justin(): HasMany
    {
        return $this->hasMany(JustinWarehouse::class, 'city_id');
    }

    public function meest(): HasMany
    {
        return $this->hasMany(MeestWarehouse::class, 'city_id');
    }

    public function regions(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function settlements(): BelongsTo
    {
        return $this->belongsTo(Settlement::class, 'type_id');
    }

    //Атрибут ref, содержащий уникальный UUID в справочнике перевозчика.
    public function getRefAttribute(): ?string
    {
        return $this->attributes['ref'] ?? null;
    }
}

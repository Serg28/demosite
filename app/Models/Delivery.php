<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends BaseModel
{
    protected $table = 'deliveries';

    protected $fillable = [];

    public $timestamps = false;

    public $pickup_id = 1;

    public $curier_id = 3;

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(
            City::class,
            'city_delivery',
            'delivery_id',
            'city_id'
        );
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            PayMethod::class,
            'delivery_payment',
            'delivery_id',
            'payment_id'
        );
    }

    public function points(): HasMany
    {
        return $this->hasMany(DeliveryPickupPoint::class)->orderBy('priority', 'asc');
    }

    public function deliverySchedules()
    {
        return $this->hasMany(DeliverySchedule::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'delivery_schedules')
            ->using(DeliverySchedule::class)
            ->withPivot('day_of_week', 'start_time', 'end_time', 'days_to_delivery');
    }
}

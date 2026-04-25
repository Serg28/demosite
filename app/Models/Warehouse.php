<?php

namespace App\Models;

class Warehouse extends BaseModel
{
    protected $table = 'warehouses';

    protected $fillable = [];

    public $timestamps = true;

    public function deliverySchedules()
    {
        return $this->hasMany(DeliverySchedule::class);
    }

    public function warehouseDeliverySchedules()
    {
        return $this->hasMany(WarehouseDeliveryScheduleInfo::class);
    }

    public function deliveries()
    {
        return $this->belongsToMany(Delivery::class, 'warehouse_delivery_schedule_info')
            ->using(WarehouseDeliveryScheduleInfo::class)
            ->withPivot('day_of_week', 'start_time', 'end_time', 'days_to_delivery');
    }
}

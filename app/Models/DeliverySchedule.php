<?php

namespace App\Models;

class DeliverySchedule extends BaseModel
{
    protected $table = 'delivery_schedules';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseDeliveryScheduleInfo()
    {
        return $this->hasMany(WarehouseDeliveryScheduleInfo::class, 'delivery_schedules_id');
    }

    public function delivery()
    {
        return $this->warehouseDeliveryScheduleInfo()->first()->delivery();
    }

    public function deliveryInfo()
    {
        return $this->hasMany(WarehouseDeliveryScheduleInfo::class, 'delivery_schedules_id');
    }
}

<?php

namespace App\Models;

class WarehouseDeliveryScheduleInfo extends BaseModel
{
    protected $table = 'warehouse_delivery_schedule_info';

    public function deliverySchedule()
    {
        return $this->belongsTo(DeliverySchedule::class, 'delivery_schedules_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}

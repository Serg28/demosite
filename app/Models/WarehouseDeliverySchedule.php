<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDeliverySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'days_to_delivery',
        'created_at',
        'updated_at',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function deliveries()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }
}

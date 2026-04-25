<?php

namespace App\Models;

class OrderProductOption extends BaseModel
{
    protected $table = 'order_product_options';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function orderProduct()
    {
        return $this->belongsTo(OrderProducts::class);
    }

    public function characteristic()
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function characteristicOption()
    {
        return $this->belongsTo(CharacteristicOption::class);
    }
}

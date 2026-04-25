<?php

namespace App\Models;

class OrderReceipt extends BaseModel
{
    protected $table = 'order_receipts';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function formPrintPath()
    {
        return config('services.checkbox_ua.domain').'/api/v1/receipts/'.$this->uuid;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

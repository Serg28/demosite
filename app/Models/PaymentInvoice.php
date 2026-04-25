<?php

namespace App\Models;

class PaymentInvoice extends BaseModel
{
    protected $fillable = [
        'order_id',
        'invoice_id',
        'order_reference_id',
        'payment_id',
        'payment_method',
        'payment_info',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getOrder()
    {
        return $this->order;
    }
}

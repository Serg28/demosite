<?php

namespace App\Models;

class OrderPayment extends BaseModel
{
    protected $table = 'order_payments';

    protected $fillable = [];

    public function legalEntitiesRecipient()
    {
        return $this->belongsTo(LegalEntitiesRecipient::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function types()
    {
        return [
            'prepayment' => 'Аванс',
            'main_payment' => 'Основная проплата',
        ];
    }
}

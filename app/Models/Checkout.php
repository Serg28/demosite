<?php

namespace App\Models;

use App\Interfaces\Checkout as CheckoutContract;
use App\Services\Checkouts\LiqPay\LiqPay;
use App\Services\Checkouts\LiqPay\LiqPayCOD;
use InvalidArgumentException;

class Checkout extends BaseModel
{
    public $timestamps = false;

    protected $guarded = [];

    public function pay(): CheckoutContract
    {
        switch ($this->slug) {
            case 'liqpay':
                return new LiqPay();
            case 'liqpaycod':
                return new LiqPayCOD();
            default:
                throw new InvalidArgumentException('Pay method not found!');
        }
    }
}

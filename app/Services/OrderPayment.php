<?php

namespace App\Services;

use App\Models\Order as OrderModel;

class OrderPayment
{
    private OrderModel $order;

    public function __construct(OrderModel $order)
    {
        $this->order = $order;
    }

    //Сумма всех оплаченных платежей в заказе
    public function cost(): int
    {
        return $this->order->payments()->where('is_payed', 1)->sum('price');
    }

    //Сумма предоплаты (которая уже зафиксирована в платежах - или вручную, или после оплаты через сайт)
    public function prepayment_cost(): int
    {
        return $this->order->payments()->where('id', $this->order->prepayment_id)->sum('price');
    }

    //Сумма всех платежей в заказе (оплаченных и неоплаченных)
    public function costAll(): int
    {
        return $this->order->payments()->sum('price');
    }

    //Оставшаяся неоплаченная сумма заказа
    public function remaining(): int
    {
        //return $this->order->cost - $this->cost();
        return ($this->order->is_delivery_paid_separately) ?
            $this->order->cost - $this->cost() :
            ($this->order->cost + $this->order->price_delivery) - $this->cost();
    }
}

<?php

namespace App\Services;

use App\Models\PayMethod;

class Payment
{
    public function get()
    {
        return session()->get('payment') ?? collect();
    }

    public function setPayment($payment_id = null): void
    {
        if (!$payment_id) {
            $this->resetPayment();
            return;
        }

        //получение данных о доставке
        $payment = PayMethod::where('id', '=', $payment_id)->active()->first();

        session()->put('payment', $payment);
    }

    public function resetPayment(): mixed
    {
        return session()->remove('payment');
    }

    public function removePayment()
    {
        return $this->resetPayment();
    }
}

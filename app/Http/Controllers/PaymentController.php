<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function init(Order $order)
    {
        return $order->pay()?->init();
    }

    public function result(Order $order): RedirectResponse
    {
        request()->session()->flash('order', $order);

        return redirect()->route('checkout.complete');
    }

    public function confirm(Order $order): void
    {
        $order->pay()?->confirm();
    }
}

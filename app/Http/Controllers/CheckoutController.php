<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Linecore\Shoppingcart\Facades\Cart;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Cart::count() === 0) {
            return redirect()->route('home')
                ->with('info', __t('Додайте товари в кошик перед оформленням замовлення'));
        }

        return view('checkout.index');
    }

    public function success(Order $order): View
    {
        abort_unless(
            session('checkout_order_id') === $order->id
            || (auth()->check() && auth()->id() === $order->user_id),
            403,
        );

        $order->load(['delivery', 'payMethod', 'products']);

        return view('checkout.success', compact('order'));
    }
}

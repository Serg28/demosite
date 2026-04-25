<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderCreate;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\UnfinishedBasket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class UnFinishBasketController extends Controller
{
    public function createOrder(UnfinishedBasket $unfinishedBasket): RedirectResponse
    {
        $order = Order::create([
            'user_id' => $unfinishedBasket->user_id,
            'first_name' => $unfinishedBasket->user->first_name ?? '',
            'last_name' => $unfinishedBasket->user->last_name ?? '',
            'phone' => $unfinishedBasket->user->phone ?? '',
            'email' => $unfinishedBasket->user->email ?? '',
            'order_status_id' => 1,
            'cost' => $this->getCostOrder($unfinishedBasket),
        ]);

        foreach ($unfinishedBasket->products as $product) {
            $productOrder = new OrderProducts([
                'product_id' => $product->product_id,
                'count' => $product->count,
                'price' => $product->price,
            ]);

            $order->products()->save($productOrder);
        }

        $unfinishedBasket->delete();
        OrderCreate::dispatch($order);

        return redirect('/admin/orders?id='.$order->id);
    }

    private function getCostOrder(UnfinishedBasket $unfinishedBasket): int
    {
        return $unfinishedBasket->products->map(function ($product) {
            return  $product->count * $product->price;
        })->sum();
    }
}

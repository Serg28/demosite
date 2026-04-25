<?php

namespace App\Services;

use App\Models\Order;
use Bschmitt\Amqp\Facades\Amqp;
use Illuminate\Support\Facades\Log;

class RabbitMqSendOrder
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function send(): void
    {
        $message = json_encode([
            'order' => $this->order->id,
            'name' => $this->order->name,
            'phone' => $this->order->phone,
            'email' => $this->order->email,
        ]);

        Log::info($message);

        Amqp::publish('routing-key', $message, ['queue' => env('AMQP_QUEUE', 'jobs')]);
    }

    private function products(Order $order): array
    {
        $products = $order->products->map(function ($item) {
            return [
                'id' => $item->product->id,
                'price' => $item->price,
                'count' => $item->count,
            ];
        });

        return $products->toArray();
    }
}

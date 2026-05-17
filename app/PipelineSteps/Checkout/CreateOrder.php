<?php

namespace App\PipelineSteps\Checkout;

use App\Actions\Checkout\RegisterUser;
use App\DTO\Checkout\CheckoutContext;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PromoCode;
use Closure;
use Illuminate\Support\Facades\DB;

final class CreateOrder
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        DB::transaction(function () use ($context): void {
            $order = Order::create([
                'first_name' => $context->firstName,
                'last_name' => $context->lastName,
                'phone' => $context->phone,
                'email' => $context->email,
                'comment'     => $context->comment,
                'call_me'     => $context->callMe,
                'register_me' => $context->registerMe,
                'receiver_first_name' => $context->receiver === 'other' ? $context->receiverFirstName : null,
                'receiver_last_name' => $context->receiver === 'other' ? $context->receiverLastName : null,
                'receiver_patronymic' => $context->receiver === 'other' ? $context->receiverPatronymic : null,
                'receiver_phone' => $context->receiver === 'other' ? $context->receiverPhone : null,
                'receiver_email' => $context->receiver === 'other' ? $context->receiverEmail : null,
                'user_id' => $context->userId,
                'delivery_id' => $context->delivery?->id,
                'pay_method_id' => $context->payMethod?->id,
                'city_id' => $context->cityId,
                'delivery_warehouse_id' => $context->deliveryWarehouseId,
                'delivery_pickup_point_id' => $context->deliveryPickupPointId,
                'address' => $context->address,
                'price_delivery' => $context->deliveryPrice,
                'cost' => $context->total,
                'cost_without_sale' => $context->subtotal,
                'sale' => $context->discountTotal,
                'sale_promo' => $context->discountBreakdown->promoCode,
                'sale_discount' => $context->discountBreakdown->discountCard,
                'payment_commission' => $context->taxes->paymentCommission,
                'order_status_id' => 1, // Новий
            ]);

            foreach ($context->cartItems as $item) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'title' => $item->name,
                    'count' => $item->qty,
                    'price' => $item->price,
                    'base_price' => $item->options->get('old_price', $item->price),
                    'amount' => round($item->price * $item->qty, 2),
                ]);
            }

            $context->order = $order;

            app(RegisterUser::class)->handle($order);

            foreach ($context->appliedDiscounts as $discount) {
                if (! ($discount instanceof PromoCode)) {
                    continue;
                }

                if ($discount->usage_type === 'once') {
                    $discount->update(['is_used' => true]);
                }

                $discount->increment('used_count');
            }
        });

        return $next($context);
    }
}

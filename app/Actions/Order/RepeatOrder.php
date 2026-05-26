<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Auth\Access\AuthorizationException;

final class RepeatOrder
{
    public function __construct(private readonly CartService $cart) {}

    /**
     * Додає товари замовлення до кошика.
     *
     * @throws AuthorizationException якщо замовлення не належить користувачу
     */
    public function handle(Order $order, User $user): int
    {
        if ($order->user_id !== $user->id) {
            throw new AuthorizationException(__t('Недостатньо прав.'));
        }

        $order->loadMissing('products');

        $added = 0;

        foreach ($order->products as $item) {
            if ($item->product_id === null) {
                continue;
            }

            $this->cart->add(
                id: $item->product_id,
                name: $item->title ?? '',
                qty: max(1, (int) $item->count),
                price: (float) $item->price,
            );

            $added++;
        }

        return $added;
    }
}

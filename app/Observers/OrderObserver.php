<?php

namespace App\Observers;

use App\Models\CumulativeDiscount;
use App\Models\Order;
use App\Models\User;

class OrderObserver
{
    /**
     * Статус «Виконано» (order_statuses.id = 11).
     * Оновлення відбувається тільки при переході в цей статус.
     */
    private const COMPLETED_STATUS = 11;

    /**
     * Перераховуємо накопичувальну знижку користувача,
     * коли замовлення переходить у статус «Виконано».
     */
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('order_status_id')) {
            return;
        }

        if ($order->order_status_id !== self::COMPLETED_STATUS) {
            return;
        }

        $user = $order->user;

        if (! $user instanceof User) {
            return;
        }

        $this->recalculateDiscount($user);
    }

    protected function recalculateDiscount(User $user): void
    {
        $cost = $user->orders()
            ->where('order_status_id', self::COMPLETED_STATUS)
            ->sum('cost');

        $discount = CumulativeDiscount::query()
            ->where('total_sum', '<=', $cost)
            ->orderByDesc('total_sum')
            ->value('discount') ?? 0;

        $user->update(['discount_cumulative' => $discount]);
    }
}

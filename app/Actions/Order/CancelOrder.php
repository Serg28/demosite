<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use RuntimeException;

final class CancelOrder
{
    /** id статусу "Скасовано клієнтом" */
    private const CANCELLED_BY_CLIENT_STATUS_ID = 12;

    /**
     * @throws AuthorizationException якщо замовлення не належить користувачу
     * @throws RuntimeException якщо замовлення не можна скасувати
     */
    public function handle(Order $order, User $user): void
    {
        if ($order->user_id !== $user->id) {
            throw new AuthorizationException(__t('Недостатньо прав для скасування замовлення.'));
        }

        if (! $order->canBeCancelled()) {
            throw new RuntimeException(__t('Замовлення не може бути скасовано.'));
        }

        $order->update(['order_status_id' => self::CANCELLED_BY_CLIENT_STATUS_ID]);
    }
}

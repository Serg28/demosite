<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\User;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\PaymentInvoiceService;
use Illuminate\Auth\Access\AuthorizationException;
use RuntimeException;

final class InitiateOrderPayment
{
    public function __construct(
        private readonly GatewayRegistry $registry,
        private readonly PaymentInvoiceService $invoiceService,
    ) {}

    /**
     * Ініціює або відновлює оплату замовлення.
     * Повертає URL платіжного шлюзу.
     *
     * @throws AuthorizationException якщо замовлення не належить користувачу
     * @throws RuntimeException якщо оплата неможлива
     */
    public function handle(Order $order, User $user): string
    {
        if ($order->user_id !== $user->id) {
            throw new AuthorizationException(__t('Недостатньо прав.'));
        }

        if (! $order->canBeRepaid()) {
            throw new RuntimeException(__t('Оплата для цього замовлення недоступна.'));
        }

        $payMethod = $order->payMethod;

        if (! $this->registry->has($payMethod->gateway)) {
            throw new RuntimeException(__t('Платіжний метод недоступний.'));
        }

        // Повертаємо існуючий URL якщо є ініційований інвойс
        $existing = $order->paymentInvoices()
            ->where('status', 'initiated')
            ->whereNotNull('payment_url')
            ->latest()
            ->first();

        if ($existing !== null) {
            return $existing->payment_url;
        }

        // Створюємо новий інвойс та ініціюємо оплату
        $invoice = $this->invoiceService->create($order, (float) $order->cost);
        $redirectUrl = $this->registry->resolve($payMethod->gateway)->init($order);
        $this->invoiceService->markInitiated($invoice, $redirectUrl);

        return is_string($redirectUrl) ? $redirectUrl : ($redirectUrl['url'] ?? '');
    }
}

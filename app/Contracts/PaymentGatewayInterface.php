<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\PaymentInvoice;

interface PaymentGatewayInterface
{
    /**
     * Ініціалізація оплати — повертає URL для редиректу або дані форми.
     */
    public function init(Order $order): string|array;

    /**
     * Перевірка статусу оплати.
     */
    public function status(PaymentInvoice $invoice): string;

    /**
     * Підтвердження оплати (webhook / callback).
     */
    public function confirm(array $payload): bool;
}

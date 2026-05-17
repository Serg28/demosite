<?php

namespace App\Actions\Checkout;

use App\Models\Order;
use App\Services\GiftCertificateService;

class CancelOrder
{
    public function __construct(private readonly GiftCertificateService $certificateService) {}

    public function handle(Order $order): void
    {
        $order->update(['order_status_id' => 10]); // Скасований

        // Повернення подарункових сертифікатів
        if ($order->giftCertificates()->exists()) {
            $this->certificateService->refund($order);
        }
    }
}

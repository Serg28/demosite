<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\PaymentInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Linecore\Shoppingcart\Facades\Cart;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly PaymentInvoiceService $invoiceService,
        private readonly GatewayRegistry $gatewayRegistry,
    ) {}

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

        $invoice = $this->invoiceService->latestForOrder($order);

        if ($invoice && ! in_array($invoice->status, ['paid', 'failed'], true)) {
            $gatewaySlug = $order->payMethod?->gateway;

            if ($gatewaySlug && $this->gatewayRegistry->has($gatewaySlug)) {
                try {
                    $status = $this->gatewayRegistry->resolve($gatewaySlug)->status($invoice);

                    if ($status === 'paid') {
                        $this->invoiceService->markPaid($invoice);
                    } elseif ($status === 'failed') {
                        $this->invoiceService->markFailed($invoice, 'Payment failed on success page check');
                    }
                } catch (\Throwable $e) {
                    Log::channel('payments')->error('Status check failed on success page', [
                        'order_id' => $order->id,
                        'gateway'  => $gatewaySlug,
                        'error'    => $e->getMessage(),
                    ]);
                }
            }
        }

        return view('checkout.success', compact('order', 'invoice'));
    }
}

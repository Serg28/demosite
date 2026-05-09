<?php

namespace App\Livewire\Checkout;

use App\Actions\Checkout\ApplyPromoCodeAction;
use App\Actions\Checkout\PlaceOrderAction;
use App\DTO\Checkout\CheckoutContext;
use App\Livewire\Concerns\HasNotifications;
use App\Models\Delivery;
use App\Models\PayMethod;
use App\Models\PromoCode;
use App\Services\Delivery\DeliveryService;
use App\Services\Payment\CommissionCalculator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutForm extends Component
{
    use HasNotifications;

    // ── Контактні дані ──────────────────────────────────────────────────────
    public string $firstName = '';
    public string $lastName = '';
    public string $phone = '';
    public string $email = '';
    public string $comment = '';

    // ── Доставка ────────────────────────────────────────────────────────────
    public ?int $deliveryId = null;

    // Деталі доставки (заповнює DeliverySelector)
    public ?int $cityId = null;
    public ?int $deliveryWarehouseId = null;
    public ?int $deliveryPickupPointId = null;
    public string $address = '';

    // ── Оплата ──────────────────────────────────────────────────────────────
    public ?int $payMethodId = null;

    // ── Промокод ────────────────────────────────────────────────────────────
    public string $promoCodeInput = '';
    public bool $promoApplied = false;
    public float $promoDiscount = 0.0;
    public string $promoMessage = '';

    #[Locked]
    public ?int $promoCodeId = null;

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $parts = explode(' ', $user->name ?? '', 2);
            $this->firstName = $parts[0] ?? '';
            $this->lastName = $parts[1] ?? '';
            $this->email = $user->email ?? '';
        }

        $first = $this->deliveries->first();
        if ($first !== null) {
            $this->deliveryId = $first->id;
        }

        $firstPay = $this->payMethods->first();
        if ($firstPay !== null) {
            $this->payMethodId = $firstPay->id;
        }
    }

    #[Computed]
    public function deliveries(): Collection
    {
        return Delivery::query()->active()->get();
    }

    #[Computed]
    public function payMethods(): Collection
    {
        if ($this->deliveryId === null) {
            return PayMethod::query()->active()->get();
        }

        return PayMethod::query()
            ->active()
            ->whereHas('deliveries', fn ($q) => $q->where('deliveries.id', $this->deliveryId))
            ->get();
    }

    #[Computed]
    public function selectedDelivery(): ?Delivery
    {
        if ($this->deliveryId === null) {
            return null;
        }

        return $this->deliveries->firstWhere('id', $this->deliveryId);
    }

    #[Computed]
    public function selectedPayMethod(): ?PayMethod
    {
        if ($this->payMethodId === null) {
            return null;
        }

        return $this->payMethods->firstWhere('id', $this->payMethodId);
    }

    #[Computed]
    public function cartItems(): Collection
    {
        return Cart::content();
    }

    #[Computed]
    public function cartCount(): int
    {
        return (int) Cart::count();
    }

    #[Computed]
    public function subtotal(): float
    {
        return (float) Cart::subtotal(2, '.', '');
    }

    #[Computed]
    public function deliveryPrice(): float
    {
        $delivery = $this->selectedDelivery;
        if ($delivery === null) {
            return 0.0;
        }

        $service = app(DeliveryService::class);
        $ctx = new CheckoutContext();
        $ctx->subtotal = $this->subtotal;

        return $service->isFree($delivery, $this->subtotal - $this->promoDiscount)
            ? 0.0
            : $service->calculatePrice($delivery, $ctx);
    }

    #[Computed]
    public function commissionAmount(): float
    {
        $payMethod = $this->selectedPayMethod;
        if ($payMethod === null) {
            return 0.0;
        }

        $base = $this->subtotal - $this->promoDiscount + $this->deliveryPrice;

        return app(CommissionCalculator::class)->calculate($payMethod, max(0.0, $base));
    }

    #[Computed]
    public function total(): float
    {
        return max(0.0,
            $this->subtotal
            - $this->promoDiscount
            + $this->deliveryPrice
            + $this->commissionAmount,
        );
    }

    #[Computed]
    public function isInstallmentsGateway(): bool
    {
        $payMethod = $this->selectedPayMethod;

        return $payMethod !== null
            && in_array($payMethod->gateway, ['monopayparts', 'privatpayparts'], true);
    }

    public function updatedDeliveryId(): void
    {
        // скидаємо оплату якщо несумісна з новою доставкою
        if ($this->payMethodId !== null && ! $this->payMethods->contains('id', $this->payMethodId)) {
            $this->payMethodId = $this->payMethods->first()?->id;
        }

        // скидаємо деталі доставки — DeliverySelector отримає новий key і re-mount
        $this->cityId = null;
        $this->deliveryWarehouseId = null;
        $this->address = '';
        $this->deliveryPickupPointId = null;
    }

    #[On('delivery-details-updated')]
    public function onDeliveryDetailsUpdated(
        ?int $cityId = null,
        ?int $deliveryWarehouseId = null,
        string $address = '',
        ?int $deliveryPickupPointId = null,
    ): void {
        $this->cityId = $cityId;
        $this->deliveryWarehouseId = $deliveryWarehouseId;
        $this->address = $address;
        $this->deliveryPickupPointId = $deliveryPickupPointId;
    }

    public function applyPromoCode(): void
    {
        $code = trim($this->promoCodeInput);

        if ($code === '') {
            $this->promoMessage = __t('Введіть промокод');
            $this->promoApplied = false;

            return;
        }

        $result = app(ApplyPromoCodeAction::class)->handle($code, $this->subtotal);

        if (! $result['success']) {
            $this->promoMessage = $result['message'];
            $this->promoApplied = false;

            return;
        }

        /** @var PromoCode $promo */
        $promo = $result['promo'];
        $this->promoCodeId = $promo->id;
        $this->promoDiscount = (float) $result['discount'];
        $this->promoApplied = true;
        $this->promoMessage = __t('Промокод застосовано! Знижка: :amount', [
            'amount' => number_format($this->promoDiscount, 0, '.', ' '),
        ]);
    }

    public function removePromoCode(): void
    {
        $this->promoCodeId = null;
        $this->promoDiscount = 0.0;
        $this->promoApplied = false;
        $this->promoCodeInput = '';
        $this->promoMessage = '';
    }

    public function placeOrder(): void
    {
        $this->validate([
            'firstName'  => 'required|string|min:2|max:100',
            'lastName'   => 'nullable|string|max:100',
            'phone'      => 'required|string|min:10|max:20',
            'email'      => 'nullable|email|max:255',
            'comment'    => 'nullable|string|max:1000',
            'deliveryId' => 'required|exists:deliveries,id',
            'payMethodId' => 'required|exists:pay_methods,id',
        ]);

        if ($this->cartCount === 0) {
            $this->notifyError(__t('Кошик порожній'));

            return;
        }

        $context = new CheckoutContext();
        $context->cartItems = $this->cartItems;
        $context->subtotal = $this->subtotal;
        $context->firstName = $this->firstName;
        $context->lastName = $this->lastName;
        $context->phone = $this->phone;
        $context->email = $this->email;
        $context->comment = $this->comment;
        $context->userId = auth()->id();
        $context->delivery = $this->selectedDelivery;
        $context->payMethod = $this->selectedPayMethod;
        $context->cityId = $this->cityId;
        $context->deliveryWarehouseId = $this->deliveryWarehouseId;
        $context->deliveryPickupPointId = $this->deliveryPickupPointId;
        $context->address = $this->address;

        if ($this->promoCodeId !== null) {
            $promo = PromoCode::find($this->promoCodeId);
            if ($promo !== null) {
                $context->appliedDiscounts[] = $promo;
            }
        }

        $result = app(PlaceOrderAction::class)->handle($context);

        if (! $result->success) {
            $this->notifyError($result->errorMessage ?? __t('Помилка оформлення замовлення'));

            return;
        }

        session(['checkout_order_id' => $result->order->id]);

        if ($result->redirectUrl !== null) {
            $this->redirect($result->redirectUrl, navigate: false);

            return;
        }

        $this->redirect(route('checkout.success', $result->order), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.checkout.checkout-form');
    }
}

<?php

namespace App\Livewire\Checkout;

use App\Actions\Checkout\ApplyDiscountCard;
use App\Actions\Checkout\ApplyPromoCode;
use App\Actions\Checkout\PlaceOrder;
use App\Services\GiftCertificateService;
use App\DTO\Checkout\CheckoutContext;
use App\Http\Requests\CheckoutRequest;
use App\Livewire\Concerns\HasNotifications;
use App\Models\Delivery;
use App\Models\DiscountCard;
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

    // ── Місто (вибирається в секції контактів) ──────────────────────────────
    public ?int $cityId = null;

    public string $cityTitle = '';

    // ── Доставка ────────────────────────────────────────────────────────────
    public ?int $deliveryId = null;

    // Деталі доставки (заповнює DeliverySelector)
    public ?int $deliveryWarehouseId = null;

    public ?int $deliveryPickupPointId = null;

    // Кур'єрська адреса (структуровані поля з DeliverySelector)
    public string $street = '';

    public string $house = '';

    public string $apartment = '';

    public string $building = '';

    public string $floor = '';

    public bool $isElevator = false;

    public bool $isLifting = false;

    // ── Оплата ──────────────────────────────────────────────────────────────
    public ?int $payMethodId = null;

    // Підформа розстрочки (monopayparts / privatpayparts)
    public ?int $payPartsCount = null;

    // Підформа безготівкового розрахунку (paylink)
    public string $b2bCompany = '';
    public string $b2bEdrpou = '';

    // ── Промокод ────────────────────────────────────────────────────────────
    public string $promoCodeInput = '';

    public bool $promoApplied = false;

    public float $promoDiscount = 0.0;

    public string $promoMessage = '';

    #[Locked]
    public ?int $promoCodeId = null;

    // ── Дисконтна картка ────────────────────────────────────────────────────
    public string $cardInput = '';

    public bool $cardApplied = false;

    public float $cardDiscount = 0.0;

    public string $cardMessage = '';

    #[Locked]
    public ?int $cardId = null;

    // ── Одержувач ────────────────────────────────────────────────────────────
    public string $receiver = 'user';

    public string $receiverFirstName = '';

    public string $receiverLastName = '';

    public string $receiverPatronymic = '';

    public string $receiverPhone = '';

    public string $receiverEmail = '';

    // ── Додатково ─────────────────────────────────────────────────────────────
    public bool $callMe = false;

    // ── Подарунковий сертифікат ──────────────────────────────────────────────
    public string $giftCodeInput = '';

    public string $giftMessage = '';

    // ── Валідність кроків (в state → доступні через $wire в Alpine) ──────────
    public bool $contactsStepValid = false;

    public bool $deliveryStepValid = false;

    public bool $paymentStepValid = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $parts = explode(' ', $user->name ?? '', 2);
            $this->firstName = $parts[0] ?? '';
            $this->lastName = $parts[1] ?? '';
            $this->email = $user->email ?? '';
        }

        $this->recalcStepValidity();
    }

    // ── Вибір міста (Alpine autocomplete → $wire.selectCity) ────────────────

    public function selectCity(int $id, string $title): void
    {
        $this->cityId = $id;
        $this->cityTitle = $title;
        $this->resetDeliveryStep();
        $this->resetPaymentStep();
        unset($this->deliveries, $this->payMethods);
        $this->recalcStepValidity();
    }

    public function clearCity(): void
    {
        $this->cityId = null;
        $this->cityTitle = '';
        $this->resetDeliveryStep();
        $this->resetPaymentStep();
        unset($this->deliveries, $this->payMethods);
        $this->recalcStepValidity();
    }

    private function resetDeliveryStep(): void
    {
        $this->deliveryId = null;
        $this->deliveryWarehouseId = null;
        $this->deliveryPickupPointId = null;
        $this->street = '';
        $this->house = '';
        $this->apartment = '';
        $this->building = '';
        $this->floor = '';
        $this->isElevator = false;
        $this->isLifting = false;
    }

    private function resetPaymentStep(): void
    {
        $this->payMethodId = null;
        $this->payPartsCount = null;
        $this->b2bCompany = '';
        $this->b2bEdrpou = '';
    }

    // ── Перерахунок валідності кроків ────────────────────────────────────────

    private function recalcStepValidity(): void
    {
        $this->contactsStepValid = $this->firstName !== ''
            && $this->phone !== ''
            && $this->cityId !== null;

        $deliveryFormComplete = false;
        if ($this->deliveryId !== null && $this->selectedDelivery !== null) {
            $deliveryFormComplete = true;
            foreach ($this->deliverySubRules() as $field => $rules) {
                if (in_array('required', (array) $rules) && empty($this->$field)) {
                    $deliveryFormComplete = false;
                    break;
                }
            }
        }
        $this->deliveryStepValid = $this->deliveryId !== null && $deliveryFormComplete;

        $paymentValid = false;
        if ($this->payMethodId !== null) {
            $paymentValid = true;
            foreach ($this->paymentSubRules() as $field => $rules) {
                if (in_array('required', (array) $rules) && empty($this->$field)) {
                    $paymentValid = false;
                    break;
                }
            }
        }
        $this->paymentStepValid = $paymentValid;
    }

    // Викликається Livewire при кожному wire:model оновленні
    public function updated(): void
    {
        $this->recalcStepValidity();
    }

    // ── Deliveries / PayMethods ──────────────────────────────────────────────

    #[Computed]
    public function deliveries(): Collection
    {
        return Delivery::query()->active()->forCity($this->cityId)->get();
    }

    #[Computed]
    public function payMethods(): Collection
    {
        if ($this->deliveryId === null) {
            return collect();
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
    public function appliedGiftCertificates(): Collection
    {
        return app(GiftCertificateService::class)->getAppliedCertificates($this->subtotal);
    }

    public function updatedDeliveryId(): void
    {
        if ($this->payMethodId !== null && ! $this->payMethods->contains('id', $this->payMethodId)) {
            $this->resetPaymentStep();
        }

        $this->deliveryWarehouseId = null;
        $this->deliveryPickupPointId = null;
        $this->recalcStepValidity();
    }

    public function updatedPayMethodId(): void
    {
        $this->payPartsCount = null;
        $this->b2bCompany = '';
        $this->b2bEdrpou = '';
        $this->recalcStepValidity();
    }

    // ── Helpers: правила підформ з конфігу ──────────────────────────────────

    /** @return array<string, array<string>> */
    private function deliverySubRules(): array
    {
        $slug = $this->selectedDelivery?->slug;
        $cfg = $slug ? config("checkout.delivery_fields.{$slug}", []) : [];
        $rules = [];
        foreach ($cfg['required'] ?? [] as $field) {
            $rules[$field] = ['required'];
        }
        foreach ($cfg['optional'] ?? [] as $field) {
            $rules[$field] = ['nullable'];
        }

        return $rules;
    }

    /** @return array<string, array<string>> */
    private function paymentSubRules(): array
    {
        $gateway = $this->selectedPayMethod?->gateway;

        return $gateway ? config("checkout.payment_fields.{$gateway}", []) : [];
    }

    #[On('delivery-details-updated')]
    public function onDeliveryDetailsUpdated(
        ?int $deliveryWarehouseId = null,
        string $street = '',
        string $house = '',
        string $apartment = '',
        string $building = '',
        string $floor = '',
        bool $isElevator = false,
        bool $isLifting = false,
        ?int $deliveryPickupPointId = null,
    ): void {
        $this->deliveryWarehouseId = $deliveryWarehouseId;
        $this->street = $street;
        $this->house = $house;
        $this->apartment = $apartment;
        $this->building = $building;
        $this->floor = $floor;
        $this->isElevator = $isElevator;
        $this->isLifting = $isLifting;
        $this->deliveryPickupPointId = $deliveryPickupPointId;
        $this->recalcStepValidity();
    }

    // ── Cart ─────────────────────────────────────────────────────────────────

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
        return (float) Cart::subtotal(
            config('cart.format.decimals', 2),
            config('cart.format.decimal_point', '.'),
            config('cart.format.thousand_separator', ''),
        );
    }

    // ── Totals ───────────────────────────────────────────────────────────────

    #[Computed]
    public function deliveryPrice(): float
    {
        $delivery = $this->selectedDelivery;
        if ($delivery === null) {
            return 0.0;
        }

        $service = app(DeliveryService::class);
        $ctx = new CheckoutContext;
        $ctx->subtotal = $this->subtotal;

        $totalDiscount = $this->promoDiscount + $this->cardDiscount;

        return $service->isFree($delivery, $this->subtotal - $totalDiscount)
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

        $base = $this->subtotal - $this->promoDiscount - $this->cardDiscount + $this->deliveryPrice;

        return app(CommissionCalculator::class)->calculate($payMethod, max(0.0, $base));
    }

    #[Computed]
    public function total(): float
    {
        return max(0.0,
            $this->subtotal
            - $this->promoDiscount
            - $this->cardDiscount
            + $this->deliveryPrice
            + $this->commissionAmount,
        );
    }

    // ── Промокод ────────────────────────────────────────────────────────────

    public function applyPromoCode(): void
    {
        $code = trim($this->promoCodeInput);

        if ($code === '') {
            $this->promoMessage = __t('Введіть промокод');
            $this->promoApplied = false;

            return;
        }

        $result = app(ApplyPromoCode::class)->handle($code, $this->subtotal);

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
            'amount' => $this->formatAmount($this->promoDiscount),
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

    // ── Дисконтна картка ────────────────────────────────────────────────────

    public function applyDiscountCard(): void
    {
        $code = trim($this->cardInput);

        if ($code === '') {
            $this->cardMessage = __t('Введіть номер картки');
            $this->cardApplied = false;

            return;
        }

        $result = app(ApplyDiscountCard::class)->handle($code, $this->subtotal);

        if (! $result['success']) {
            $this->cardMessage = $result['message'];
            $this->cardApplied = false;

            return;
        }

        /** @var DiscountCard $card */
        $card = $result['card'];
        $this->cardId = $card->id;
        $this->cardDiscount = (float) $result['discount'];
        $this->cardApplied = true;
        $this->cardMessage = __t('Картку застосовано! Знижка: :amount', [
            'amount' => $this->formatAmount($this->cardDiscount),
        ]);
    }

    public function removeDiscountCard(): void
    {
        $this->cardId = null;
        $this->cardDiscount = 0.0;
        $this->cardApplied = false;
        $this->cardInput = '';
        $this->cardMessage = '';
    }

    // ── Подарунковий сертифікат ──────────────────────────────────────────────

    public function applyGiftCode(): void
    {
        $code = trim(strtoupper($this->giftCodeInput));

        if ($code === '') {
            return;
        }

        $added = app(GiftCertificateService::class)->addCode($code);

        $this->giftMessage = $added
            ? __t('Сертифікат застосовано')
            : __t('Сертифікат не знайдено або вже використаний');
        $this->giftCodeInput = '';
        unset($this->appliedGiftCertificates);
    }

    public function removeGiftCode(string $code): void
    {
        app(GiftCertificateService::class)->removeCode($code);
        $this->giftMessage = '';
        unset($this->appliedGiftCertificates);
    }

    // ── Оформлення ──────────────────────────────────────────────────────────

    public function placeOrder(): void
    {
        $request = new CheckoutRequest;
        $this->validate(
            array_merge($request->rules(), $this->deliverySubRules(), $this->paymentSubRules()),
            $request->messages(),
        );

        if ($this->cartCount === 0) {
            $this->notifyError(__t('Кошик порожній'));

            return;
        }

        $context = new CheckoutContext;
        $context->cartItems = $this->cartItems;
        $context->subtotal = $this->subtotal;
        $context->firstName = $this->firstName;
        $context->lastName = $this->lastName;
        $context->phone = $this->phone;
        $context->email = $this->email;
        $context->comment = $this->comment;
        $context->userId = auth()->id();
        $context->callMe = $this->callMe;
        $context->receiver = $this->receiver;
        $context->receiverFirstName = $this->receiverFirstName;
        $context->receiverLastName = $this->receiverLastName;
        $context->receiverPatronymic = $this->receiverPatronymic;
        $context->receiverPhone = $this->receiverPhone;
        $context->receiverEmail = $this->receiverEmail;
        $context->delivery = $this->selectedDelivery;
        $context->payMethod = $this->selectedPayMethod;
        $context->cityId = $this->cityId;
        $context->deliveryWarehouseId = $this->deliveryWarehouseId;
        $context->deliveryPickupPointId = $this->deliveryPickupPointId;
        $context->address = collect([$this->street, $this->house, $this->apartment, $this->building, $this->floor])
            ->filter()
            ->implode(', ');
        $context->payPartsCount = $this->payPartsCount;
        $context->b2bCompany = $this->b2bCompany;
        $context->b2bEdrpou = $this->b2bEdrpou;

        if ($this->promoCodeId !== null) {
            $promo = PromoCode::find($this->promoCodeId);
            if ($promo !== null) {
                $context->appliedDiscounts[] = $promo;
            }
        }

        if ($this->cardId !== null) {
            $card = DiscountCard::find($this->cardId);
            if ($card !== null) {
                $context->appliedDiscounts[] = $card;
            }
        }

        $result = app(PlaceOrder::class)->handle($context);

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

    private function formatAmount(float $amount): string
    {
        return number_format(
            $amount,
            config('cart.format.decimals', 0),
            config('cart.format.decimal_point', '.'),
            config('cart.format.thousand_separator', ' '),
        );
    }

    public function render(): View
    {
        return view('livewire.checkout.checkout-form');
    }
}

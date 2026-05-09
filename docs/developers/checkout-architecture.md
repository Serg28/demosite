# Архітектура Checkout

> Узгоджено: 2026-05-09 | Статус: **реалізовано (сесія 16)** | Детально: `checkout-phase42-status.md`

## Структура файлів (строго по ARCHITECTURE_PLAN.md)

```
app/
├── Actions/Checkout/
│   ├── PlaceOrderAction.php          # запускає Pipeline
│   ├── ApplyPromoCodeAction.php
│   └── CancelOrderAction.php
│
├── Actions/Payment/
│   ├── ProcessPaymentAction.php
│   ├── RetryPaymentAction.php
│   └── HandlePaymentWebhookAction.php
│
├── Pipelines/
│   ├── CheckoutPipeline.php          # оркестратор (config-driven)
│   └── OrderStatusPipeline.php
│
├── PipelineSteps/Checkout/           # 10 кроків
│   ├── ValidateCartStep.php
│   ├── ApplyDiscountsStep.php        # промокоди + дисконтні карти
│   ├── ValidateGiftCertificatesStep.php # резерв у Cache
│   ├── CalculateDeliveryStep.php
│   ├── CalculateCommissionStep.php   # комісія платіжної системи
│   ├── CalculateTotalsStep.php       # фінальний розрахунок з TaxBreakdown
│   ├── CreateOrderStep.php
│   ├── ProcessPaymentStep.php        # gateway + certificate
│   ├── ClearCartStep.php
│   └── SendNotificationsStep.php
│
├── Gateways/                         # ПЛОСКО — один клас = один файл
│   ├── LiqPayGateway.php
│   ├── LiqPayCodGateway.php
│   ├── MonoPayGateway.php            # Holdable, Returnable
│   ├── MonoPayPartsGateway.php       # Installmentable
│   ├── WayForPayGateway.php          # Holdable
│   ├── PrivatPayPartsGateway.php     # Holdable, Installmentable
│   ├── EasyPayGateway.php
│   ├── NovaPayGateway.php
│   ├── PayLinkGateway.php
│   └── PlatonGateway.php
│
├── Contracts/
│   ├── PaymentGatewayInterface.php   # base: init(), status(), confirm()
│   ├── DeliveryServiceInterface.php
│   ├── CommissionStrategy.php
│   ├── DiscountSource.php            # getType(), getAmount(), isCompatibleWith()
│   ├── GiftCertificateBurnStrategy.php # apply(Certificate, float $orderTotal): float
│   ├── Holdable.php                  # captureHold(), releaseHold()
│   ├── Returnable.php                # return()
│   └── Installmentable.php          # getInstallments(), getCommission()
│
├── DTO/Checkout/
│   ├── CheckoutContext.php           # shared state через всі Pipes
│   ├── CheckoutResult.php
│   ├── TaxBreakdown.php              # productTax, paymentCommission, total
│   └── DiscountBreakdown.php         # promoCode, discountCard, total
│
├── Models/
│   ├── PayMethod.php
│   ├── PaymentInvoice.php            # вся історія спроб оплати
│   ├── PaymentCredential.php         # ротація ключів (day_of_week)
│   ├── PromoCode.php
│   ├── DiscountCard.php
│   ├── GiftCertificate.php           # Phase окремий
│   ├── Delivery.php
│   ├── City.php
│   ├── NPWarehouse.php
│   ├── UkrposhtaWarehouse.php
│   ├── JustinWarehouse.php
│   ├── MeestWarehouse.php
│   ├── RozetkaWarehouse.php
│   └── DeliveryPickupPoint.php
│
├── Services/Payment/                 # ПЛОСКО всередині
│   ├── GatewayRegistry.php
│   ├── CredentialResolver.php
│   ├── PaymentInvoiceService.php
│   ├── WebhookProcessor.php
│   ├── CommissionCalculator.php
│   ├── MonoPartsCommissionStrategy.php
│   ├── PrivatPPCommissionStrategy.php
│   └── FlatCommissionStrategy.php
│
├── Services/Delivery/
│   ├── DeliveryService.php
│   └── ShippingCalculator.php        # (перенос з smartmag — окремий етап)
│
├── Services/
│   └── GiftCertificateService.php    # validation, reservation, finalization
│
├── Providers/
│   ├── PaymentServiceProvider.php    # реєстрація гейтвеїв + стратегій
│   └── DeliveryServiceProvider.php
│
└── Livewire/Checkout/
    ├── CheckoutForm.php              # основний компонент
    ├── DeliverySelector.php          # вибір служби + відділення
    └── PayPartsCalculator.php        # розрахунок розстрочки live
```

---

## CheckoutContext — shared DTO через Pipeline

**Мутабельний клас** (не readonly — кожен крок Pipeline дозаповнює поля).

```php
class CheckoutContext
{
    // Cart
    public Collection $cartItems;
    public float $subtotal = 0.0;

    // Discounts
    public array $appliedDiscounts = [];  // DiscountSource[]
    public DiscountBreakdown $discountBreakdown;
    public float $discountTotal = 0.0;

    // Gift Certificates
    public array $giftCertificateCodes = [];
    public float $giftCertificateTotal = 0.0;

    // Delivery
    public ?Delivery $delivery = null;
    public float $deliveryPrice = 0.0;

    // Taxes & Commissions
    public TaxBreakdown $taxes;
    public string $taxMode;          // з config('checkout.tax_mode')
    public string $discountMode;     // з config('checkout.discount_mode')

    // Customer
    public string $firstName, $lastName, $phone, $email, $comment;
    public ?int $userId = null;

    // Delivery details
    public ?int $cityId, $npWarehouseId, $deliveryPickupPointId, ...;
    public string $address = '';

    // Payment
    public ?PayMethod $payMethod = null;
    public float $total = 0.0;
    public float $paymentAmount = 0.0;  // total - giftCertificateTotal
    public ?string $paymentRedirectUrl = null;

    // Result
    public ?Order $order = null;
    public bool $isPaidByCertificates = false;
    public bool $failed = false;
    public ?string $failReason = null;

    public function fail(string $reason): void { ... }
    public function hasFailed(): bool { ... }
}
```

---

## TaxBreakdown — розбивка податків

```php
readonly class TaxBreakdown
{
    public float $productTax;        // НДС на товари (з пакету Cart)
    public float $paymentCommission; // комісія платіжної системи
    public float $total;             // productTax + paymentCommission

    // Режими:
    // 'per_product' → tax розподілено по товарах у CartItem (поточний, для 1С)
    // 'order_level' → tax = окремий рядок у чекауті, taxRate=0 у CartItem
}
```

**Пакет Cart — без змін.** Для `order_level` режиму: `taxRate=0` на всіх CartItem,
податок рахується у `CalculateTotalsStep` і передається в CheckoutContext.

---

## DiscountSource — інтерфейс знижок

```php
interface DiscountSource
{
    public function getType(): string;   // 'promo_code' | 'discount_card'
    public function getAmount(float $subtotal): float;
    public function getLabel(): string;  // для відображення у чекауті
    public function isCompatibleWith(DiscountSource $other): bool;
}
```

**Правила сумісності** — у `config/checkout.php`:
```php
'discount_combinations' => [
    'promo_code'    => ['discount_card' => true],   // можна комбінувати
    'discount_card' => ['promo_code' => true],
],
```

---

## Gift Certificates — як платіжне джерело

**Застосовуються ПІСЛЯ** всіх знижок і комісій:
```
total = (subtotal - discounts) + delivery + paymentCommission + productTax
paymentAmount = total - giftCertificateTotal
```

**Стратегія сгорання** (`GiftCertificateBurnStrategy`):
- `BurnAllStrategy` (default) — залишок сгорає
- `KeepRemainingStrategy` — залишок залишається на сертифікаті (майбутнє)

**Резервація** через Cache (60 хв TTL, ключ `gift:reserve:{code}`):
```php
GiftCertificateService::reserve(string $code): void
GiftCertificateService::isReserved(string $code): bool
GiftCertificateService::finalize(Order $order, array $codes): void  // знімає резерв + is_used=true
GiftCertificateService::refund(Order $order): void                  // is_used=false при відміні
```

**Сумісність** (поля на моделі `GiftCertificate`):
- `use_for_discount_cards` — чи можна комбінувати з дисконтними картами
- `use_for_promotional` — чи діє на акційні товари
- `use_for_installments` — чи діє при розстрочці

---

## Порядок Pipeline кроків і розрахунків

```
1. ValidateCartStep        → cart не порожній, товари доступні
2. ApplyDiscountsStep      → promo + discount card (перевірка сумісності)
3. ValidateGiftCerts...    → перевірка кодів + резерв у Cache
4. CalculateDeliveryStep   → ціна доставки за типом
5. CalculateCommissionStep → комісія гейтвею (від суми після знижок)
6. CalculateTotalsStep     → TaxBreakdown + final total + paymentAmount
7. CreateOrderStep         → Order + OrderProducts + pivot таблиці
8. ProcessPaymentStep      → gateway.init() + cert.finalize()
9. ClearCartStep           → Cart::destroy() + UnfinishedBasket::delete()
10. SendNotificationsStep  → email/SMS в queue
```

---

## Gateway Registry — конфіг-керований

```php
// config/payment.php
'gateways' => [
    'liqpay'           => LiqPayGateway::class,
    'liqpay_cod'       => LiqPayCodGateway::class,
    'monopay'          => MonoPayGateway::class,
    'monopayparts'     => MonoPayPartsGateway::class,
    'wayforpay'        => WayForPayGateway::class,
    'privatpayparts'   => PrivatPayPartsGateway::class,
    'easypay'          => EasyPayGateway::class,
    'novapay'          => NovaPayGateway::class,
    'paylink'          => PayLinkGateway::class,
    'platon'           => PlatonGateway::class,
],
```

**Додати новий гейтвей** = 1 клас + 1 рядок у конфізі.

---

## Credential Rotation (з smartmag)

`PaymentCredential` модель (таблиця `payment_credentials`):
- `pay_method_id`, `day_of_week` (1-7), `credentials` (JSON), `is_default`

`CredentialResolver::resolve(int $payMethodId): array` — повертає ключі за поточним днем
або default. Підставляє в config перед використанням гейтвею.

---

## Discount Mode (config → майбутній setting)

```php
// config/checkout.php
'discount_mode' => env('CHECKOUT_DISCOUNT_MODE', 'per_product'),
// 'per_product' → знижка розподілена по товарах (поточний, для 1С)
// 'order_level' → знижка = окремий рядок у підсумку

'tax_mode' => env('CHECKOUT_TAX_MODE', 'per_product'),
// 'per_product' → НДС у CartItem (пакет Cart)
// 'order_level' → НДС як окремий рядок (taxRate=0 у Cart)
```

---

## API-готовність

Весь `CheckoutPipeline` приймає `CheckoutContext` — чистий DTO без HTTP-залежностей.
`CheckoutForm` Livewire будує його з вводу користувача.
Майбутній `Api/v1/CheckoutController` будує його з JSON request — той самий Pipeline.

---

## Окремі етапи (не в Phase 4.2)

| Функція | Коли |
|---|---|
| Gift Certificates повна реалізація | Phase окрема після Checkout |
| `KeepRemainingStrategy` для сертифікатів | після базової реалізації |
| Discount policy rules (складні взаємодії) | після базового DiscountEngine |
| `ShippingCalculator` (терміни доставки) | Phase 6 |
| API Checkout endpoint | Окрема API фаза |
| `order_level` tax/discount mode | після базової реалізації |

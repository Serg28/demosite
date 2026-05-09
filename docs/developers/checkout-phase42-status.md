# Phase 4.2 — CheckoutPipeline: стан реалізації

> Реалізовано: 2026-05-09 (сесія 16) | Статус: ✅ ЗАВЕРШЕНО

## Що реалізовано

### Конфіги
| Файл | Призначення |
|---|---|
| `config/checkout.php` | Pipeline кроки, discount_mode, tax_mode, gift_cert TTL |
| `config/payment.php` | Реєстр 10 гейтвеїв, стратегії комісій |

### DTOs (`app/DTO/Checkout/`)
| Клас | Призначення |
|---|---|
| `CheckoutContext` | Мутабельний shared state через всі кроки Pipeline |
| `TaxBreakdown` | productTax + paymentCommission + total |
| `DiscountBreakdown` | promoCode + discountCard + total |
| `CheckoutResult` | success(Order, redirectUrl?) / failure(message) |

### Contracts (`app/Contracts/`)
| Інтерфейс | Імплементори |
|---|---|
| `PaymentGatewayInterface` | Всі 10 гейтвеїв |
| `DiscountSource` | `PromoCode`, `DiscountCard` |
| `CommissionStrategy` | `FlatCommissionStrategy`, `MonoPartsCommissionStrategy`, `PrivatPPCommissionStrategy` |
| `GiftCertificateBurnStrategy` | (реалізація — окрема Phase) |
| `Holdable` | `MonoPayGateway`, `WayForPayGateway`, `PrivatPayPartsGateway` |
| `Returnable` | `MonoPayGateway` |
| `Installmentable` | `MonoPayPartsGateway`, `PrivatPayPartsGateway` |
| `DeliveryServiceInterface` | `DeliveryService` |

### Pipeline (`app/Pipelines/`, `app/PipelineSteps/Checkout/`)
```
CheckoutPipeline::run(CheckoutContext) → CheckoutContext

1. ValidateCartStep         → картItems + subtotal із Cart instance
2. ApplyDiscountsStep       → DiscountSource[], сумісність, DiscountBreakdown
3. ValidateGiftCertificatesStep → Cache reserve, giftCertificateTotal
4. CalculateDeliveryStep    → DeliveryService::isFree / calculatePrice
5. CalculateCommissionStep  → CommissionCalculator → TaxBreakdown.paymentCommission
6. CalculateTotalsStep      → total + paymentAmount + isPaidByCertificates
7. CreateOrderStep          → Order + OrderProduct (в транзакції)
8. ProcessPaymentStep       → GiftCertificateService.finalize + GatewayRegistry.resolve + init()
9. ClearCartStep            → Cart::destroy + UnfinishedBasket::delete
10. SendNotificationsStep   → SendOrderNotification Job (queue: notifications)
```

### Actions (`app/Actions/Checkout/`)
| Клас | Призначення |
|---|---|
| `PlaceOrderAction` | Запускає Pipeline, повертає `CheckoutResult` |
| `ApplyPromoCodeAction` | Валідація промокоду → `['success', 'promo', 'discount']` |
| `CancelOrderAction` | order_status_id=10 + GiftCertificateService.refund |

### Services (`app/Services/Payment/`, `app/Services/Delivery/`, `app/Services/`)
| Клас | Призначення |
|---|---|
| `GatewayRegistry` | `resolve(slug)` → `PaymentGatewayInterface` |
| `CredentialResolver` | По day_of_week або is_default з `payment_credentials` |
| `CommissionCalculator` | Делегує до стратегії по slug гейтвею |
| `FlatCommissionStrategy` | `amount * commission_percent / 100` |
| `MonoPartsCommissionStrategy` | Ставки по місяцях [2,4,6,9,12,18,24] |
| `PrivatPPCommissionStrategy` | Ставки по місяцях [2,4,6,10] |
| `PaymentInvoiceService` | create / markInitiated / markPaid / markFailed / latestForOrder |
| `WebhookProcessor` | process(slug, payload) → підтверджує + оновлює invoice+order |
| `DeliveryService` | isFree / calculatePrice / available(payMethodId) |
| `GiftCertificateService` | findValid / reserve / isReserved / finalize / refund |

### Гейтвеї (`app/Gateways/`) — СКЕЛЕТИ
Всі 10 реалізують `PaymentGatewayInterface`. Тіло методів — TODO для фази 4.4:
- `LiqPayGateway`, `LiqPayCodGateway`
- `MonoPayGateway` (+ `Holdable`, `Returnable`)
- `MonoPayPartsGateway` (+ `Installmentable`) — `getInstallments()` повністю реалізовано
- `WayForPayGateway` (+ `Holdable`)
- `PrivatPayPartsGateway` (+ `Holdable`, `Installmentable`) — `getInstallments()` повністю реалізовано
- `EasyPayGateway`, `NovaPayGateway`, `PayLinkGateway`, `PlatonGateway`

### Моделі
| Модель | Таблиця | Особливості |
|---|---|---|
| `PayMethod` | `pay_methods` | HasTranslations, slug, commission_percent |
| `Delivery` | `deliveries` | HasTranslations, type, free_cost, pivot payments |
| `PromoCode` | `promo_codes` | implements DiscountSource |
| `DiscountCard` | `discount_cards` | implements DiscountSource |
| `PaymentInvoice` | `payment_invoices` | status, gateway_response (JSON), paid_at |
| `PaymentCredential` | `payment_credentials` | day_of_week, is_default, credentials (JSON) |
| `GiftCertificate` | `gift_certificates` | is_used, use_for_* flags, scopeValid() |
| `City` | `cities` | HasTranslations, ref (NP) |
| `NPWarehouse` | `np_warehouses` | HasTranslations, city FK |
| `DeliveryPickupPoint` | `delivery_pickup_points` | HasTranslations, delivery FK |
| `Order` | `orders` | оновлено: всі FK + cost breakdown + giftCertificates() |
| `OrderProduct` | `order_products` | оновлено: count замість quantity, base_price, amount |

### Providers
- `PaymentServiceProvider` — реєструє всі Payment сервіси
- `DeliveryServiceProvider` — реєструє DeliveryService

### Міграція
`2026_05_09_000001_create_checkout_tables` — пройшла в prod і test DB:
- Додає `order_status_id` до `orders`
- Перейменовує `quantity`→`count`, додає `title`, `base_price`, `amount` до `order_products`

### Тести
`tests/Feature/Checkout/CheckoutPipelineTest.php` — **13 тестів ✅**

---

## Що ще не реалізовано (наступні фази)

| Задача | Фаза |
|---|---|
| Тіло гейтвеїв (API calls, підпис, redirect URL) | 4.4 |
| `Livewire/Checkout/CheckoutForm.php` | 4.3 |
| `Livewire/Checkout/DeliverySelector.php` | 4.3 |
| `Livewire/Checkout/PayPartsCalculator.php` | 4.3 |
| CheckoutController + маршрути + success page | 4.3 |
| Webhook маршрути + контролер | 4.6 |
| `GiftCertificateBurnStrategy` реалізації | Окрема Phase |
| UkrposhtaWarehouse, JustinWarehouse, MeestWarehouse моделі | 4.4 |
| Checkbox фіскалізація | 4.9 |

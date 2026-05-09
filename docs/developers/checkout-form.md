# Checkout Form — Phase 4.3

> Статус: реалізовано | Сесія 17

## Структура файлів

```
app/
├── Http/Controllers/CheckoutController.php
├── Livewire/Checkout/
│   ├── CheckoutForm.php          # головна форма
│   ├── DeliverySelector.php      # вибір доставки (місто + відділення)
│   └── PayPartsCalculator.php    # калькулятор розстрочки

resources/views/
├── checkout/
│   ├── index.blade.php           # сторінка /checkout
│   └── success.blade.php         # сторінка /checkout/success/{order}
└── livewire/checkout/
    ├── checkout-form.blade.php
    ├── delivery-selector.blade.php
    └── pay-parts-calculator.blade.php

database/
├── migrations/2026_05_09_000002_create_checkout_lookup_tables.php
└── seeders/CheckoutSeeder.php

tests/Feature/Checkout/CheckoutFormTest.php   # 15 тестів ✅
```

## Маршрути

```php
GET  /checkout                    → checkout.index
GET  /checkout/success/{order}    → checkout.success
```

## CheckoutForm

**Головний Livewire-компонент** оформлення замовлення.

### Властивості

| Властивість | Тип | Опис |
|---|---|---|
| `firstName`, `lastName` | string | Контактні дані |
| `phone`, `email`, `comment` | string | Контактні дані |
| `deliveryId` | ?int | ID обраної доставки |
| `payMethodId` | ?int | ID методу оплати |
| `cityId`, `npWarehouseId`, `address` | mixed | Деталі доставки (від DeliverySelector) |
| `promoCodeInput` | string | Введений промокод |
| `promoApplied`, `promoDiscount` | bool/float | Стан промокоду |

### Computed

- `deliveries` — активні доставки
- `payMethods` — методи оплати, сумісні з обраною доставкою
- `selectedDelivery`, `selectedPayMethod` — поточні моделі
- `subtotal`, `deliveryPrice`, `commissionAmount`, `total` — фінансові підсумки
- `isInstallmentsGateway` — чи обраний gateway є розстрочкою

### Методи

- `applyPromoCode()` — викликає `ApplyPromoCodeAction`
- `removePromoCode()` — скидає промокод
- `placeOrder()` — валідація → будує `CheckoutContext` → `PlaceOrderAction`
- `#[On('delivery-details-updated')]` — отримує деталі від `DeliverySelector`

## DeliverySelector

**Дочірній компонент** вибору деталей доставки. Монтується з:
```blade
<livewire:checkout.delivery-selector
    :deliveryId="$deliveryId"
    :deliverySlug="$this->selectedDelivery->slug"
    :wire:key="'ds-'.$deliveryId"
/>
```

`wire:key` забезпечує re-mount при зміні `deliveryId` → стан скидається.

### Слаги доставки

| Slug | UI |
|---|---|
| `np_branch`, `np_poshtamat` | Пошук міста + пошук відділення НП |
| `courier` | Поле введення адреси |
| `pickup` | Список пунктів видачі |

Після вибору: `dispatch('delivery-details-updated', cityId:..., npWarehouseId:..., ...)`.

## PayPartsCalculator

Показує таблицю розстрочки через `gateway->getInstallments($amount)`.
Монтується тільки якщо `isInstallmentsGateway === true`.

```blade
<livewire:checkout.pay-parts-calculator
    :gatewaySlug="$this->selectedPayMethod->gateway"
    :orderAmount="$this->total"
    :wire:key="'ppc-'.$payMethodId.'-'.(int)$this->total"
/>
```

## Міграція та Seeder

**`2026_05_09_000002_create_checkout_lookup_tables`** створює:
- `deliveries`, `pay_methods`, `delivery_payment`
- `promo_codes`, `discount_cards`
- `cities`, `np_warehouses`, `delivery_pickup_points`
- `payment_credentials`, `payment_invoices`, `gift_certificates`
- Нові колонки в `orders`: `first_name`, `last_name`, `user_id`, `delivery_id`, `pay_method_id`, `city_id`, `np_warehouse_id`, фінансові поля

**`CheckoutSeeder`** — 5 доставок і 5 методів оплати (dev-дані).

## Безпека

Сторінка `/checkout/success/{order}` захищена:
- Session: `checkout_order_id === $order->id`
- або Auth: `auth()->id() === $order->user_id`

## Що далі

- **4.4** — Реалізувати тіло гейтвеїв (LiqPay першим)
- **4.5** — Знижки/промокоди UI (вже є `ApplyPromoCodeAction`)
- **4.6** — Webhook/success page з реальними даними гейтвею
- Завантаження бази міст/відділень НП (фонова задача)

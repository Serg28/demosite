# Checkout — UI Forms (Phases 4.7–4.9)

> Статус: реалізовано | Сесія 36–37

## Що є

- **Phase 4.7** — `CheckoutRequest` Form Request з повною валідацією полів
- **Phase 4.8** — Покрокова форма, Alpine autocomplete для міст, config-driven sub-forms
- **Phase 4.9** — Повна верстка по еталону, sub-форми доставки, receiver/callMe, Demo layout

## Структура файлів

```
app/
├── Http/Requests/CheckoutRequest.php          # Form Request (Phase 4.7)
├── Livewire/Checkout/
│   ├── CheckoutForm.php                       # головна Livewire-форма
│   ├── DeliverySelector.php                   # вибір доставки + відділення
│   └── PayPartsCalculator.php                 # калькулятор розстрочки

resources/views/
├── checkout/
│   ├── index.blade.php                        # GET /checkout
│   └── success.blade.php                      # GET /checkout/success/{order}
└── livewire/checkout/
    ├── checkout-form.blade.php                # головний шаблон форми
    ├── delivery-selector.blade.php
    ├── pay-parts-calculator.blade.php
    ├── delivery/                              # sub-форми доставки (config-driven)
    │   ├── np_branch.blade.php               # Нова Пошта (відділення)
    │   ├── np_poshtamat.blade.php            # Нова Пошта (поштомат)
    │   ├── np_address.blade.php              # Нова Пошта (адресна)
    │   ├── courier.blade.php                 # Кур'єр
    │   ├── pickup.blade.php                  # Самовивіз
    │   ├── ukrposhta.blade.php
    │   ├── justin.blade.php
    │   ├── meest.blade.php
    │   ├── rozetka.blade.php
    │   └── _default.blade.php                # fallback
    └── payment/                              # sub-форми оплати
```

## CheckoutRequest (Phase 4.7)

Form Request для валідації при `placeOrder()`.

**Ключові правила:**

| Поле | Правило |
|------|---------|
| `firstName` | required, string, min:2, max:100 |
| `phone` | required, string, min:10, max:20 |
| `email` | nullable, email |
| `receiver` | required, in:user,other |
| `receiverFirstName` | required_if:receiver,other |
| `receiverPhone` | required_if:receiver,other |
| `cityId` | required, exists:cities,id |
| `deliveryId` | required, exists:deliveries,id |
| `payMethodId` | required, exists:pay_methods,id |

Повідомлення через `__t()` (локалізовані рядки).

## CheckoutForm — властивості

| Властивість | Тип | Опис |
|---|---|---|
| `firstName`, `lastName`, `phone`, `email`, `comment` | string | Контакти замовника |
| `receiver` | string('user'\|'other') | Хто отримувач |
| `receiverFirstName`, `receiverLastName`, `receiverPhone`, `receiverEmail` | string | Поля одержувача |
| `callMe` | bool | Перетелефонувати для уточнення |
| `cityId` | ?int | Обране місто |
| `deliveryId` | ?int | Обрана доставка |
| `payMethodId` | ?int | Обраний метод оплати |
| `deliveryWarehouseId` | ?int | ID відділення/поштомату |
| `deliveryPickupPointId` | ?int | ID самовивозу |
| `address` | string | Адреса кур'єрської доставки |
| `payPartsCount` | ?int | Місяців розстрочки |

**Computed (readonly, не через `$wire` в Alpine):**

```php
#[Computed] public function deliveries(): Collection     // доступні доставки
#[Computed] public function payMethods(): Collection     // методи для обраної доставки
#[Computed] public function subtotal(): float
#[Computed] public function deliveryPrice(): float
#[Computed] public function commissionAmount(): float
#[Computed] public function total(): float
#[Computed] public function cartCount(): int
#[Computed] public function isInstallmentsGateway(): bool
```

> **Важливо**: `#[Computed]` НЕ доступні через `$wire.propName` в Alpine. Для передачі в Alpine — використовувати `public bool $cartHasBlockingItems` (звичайна публічна властивість).

## DeliverySelector

Livewire-компонент вибору відділення.

**Props (передаються з батьківського компонента):**
```php
public ?int $deliveryId
public ?string $deliverySlug
public ?int $cityId
```

**Логіка:**
- `warehouseLabel` — береться з `config('checkout.warehouse_labels')` по slug
- `selectWarehouse(int $id, string $label)` — диспатчить `delivery-details-updated`

```php
// config/checkout.php
'warehouse_labels' => [
    'np_branch'     => 'Відділення',
    'np_poshtamat'  => 'Поштомат',
    'ukrposhta'     => 'Поштове відділення',
    // slug без запису → 'Відділення' (default)
],
```

## Sub-форми доставки (config-driven)

Кожна доставка рендерить власний шаблон:

```blade
{{-- delivery-selector.blade.php --}}
@include("livewire.checkout.delivery.{$deliverySlug}", [...])
{{-- fallback: _default.blade.php --}}
```

Поля sub-форм визначені в конфізі:

```php
// config/checkout.php
'delivery_fields' => [
    'np_branch'     => ['required' => ['warehouse'], 'optional' => []],
    'np_poshtamat'  => ['required' => ['warehouse'], 'optional' => []],
    'np_address'    => ['required' => ['street', 'house'], 'optional' => ['apartment', 'building', 'floor']],
    'courier'       => ['required' => ['street', 'house'], 'optional' => ['apartment', 'building', 'floor', 'isElevator', 'isLifting']],
    'pickup'        => ['required' => ['pickup_point'], 'optional' => []],
    // ...
],
```

## PayPartsCalculator

```php
// Props
public string $gatewaySlug  // 'monopayparts' | 'privatpayparts'
public float $orderAmount

// Computed
public array $installments  // [{months, monthlyPayment, totalAmount, commission}]
```

## Receiver / CallMe

```php
// CheckoutForm
public string $receiver = 'user';  // 'user' | 'other'
public bool $callMe = false;
```

При `receiver = 'other'` — обов'язкові `receiverFirstName` + `receiverPhone`.
При `callMe = true` — замовник вказує свої дані, але просить передзвонити для уточнень.

## Alpine Autocomplete (міста)

```js
// В checkout-form.blade.php — Alpine.data() компонент
// Викликає /api/v1/checkout/cities?q=... → список міст
// По вибору: $wire.selectCity(id, label)
```

`selectCity(int $cityId, string $label)` — скидає deliveryId + deliveryWarehouseId.

---

## Для адміністратора

### Як додати нову доставку

1. **Модель** — `Delivery::create([...])` зі slug
2. **Config** — додати slug до `delivery_fields` в `config/checkout.php`
3. **Blade** — `resources/views/livewire/checkout/delivery/{slug}.blade.php`
4. **warehouse_labels** — якщо доставка має відділення, додати label

Якщо blade немає — використовується `_default.blade.php`.

### Як додати нову доставку з прив'язкою до міст

```php
$delivery = Delivery::create([
    'title'            => json_encode(['ua' => 'Назва', 'ru' => 'Название']),
    'slug'             => 'my_delivery',
    'price'            => 100.00,
    'free_cost'        => 2000.00,
    'is_for_all_cities' => false,   // false = тільки для певних міст
    'priority'         => 30,
    'is_active'        => true,
]);
// Прив'язати до міст:
$delivery->cities()->attach([$city1->id, $city2->id]);
```

### Як прив'язати метод оплати до доставки

```php
$delivery->payments()->attach($payMethod->id);
```

### Як налаштувати кроки форми

Покрокова форма (step 1 — контакти, step 2 — доставка, step 3 — оплата) — управляється Alpine (`currentStep`). Відображення кроків не потребує серверних змін.

### Що виводиться на сторінці success

`checkout/success.blade.php` показує:
- Номер замовлення
- Суму
- Метод оплати
- Посилання для оплати (якщо redirect-платіж)

Доступ до сторінки — тільки якщо в сесії є `checkout_order_id`.

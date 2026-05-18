# Checkout — Знижки (Phase 4.5)

> Статус: реалізовано | Сесія 36

## Що є

- **Промокоди** — фіксовані або відсоткові знижки на замовлення, з обмеженням по товарах (M2M)
- **Дисконтні картки** — знижка по коду картки, percent або fixed
- **Сумісність** — можна застосувати і промокод, і картку одночасно (за config)
- **Пайплайн** — `ApplyDiscounts` крок застосовує обидва джерела через `DiscountSource` контракт

## Структура файлів

```
app/
├── Actions/Checkout/
│   ├── ApplyPromoCode.php          # перевірка + обрахунок знижки промокоду
│   └── ApplyDiscountCard.php       # перевірка + обрахунок знижки картки
├── PipelineSteps/Checkout/
│   └── ApplyDiscounts.php          # крок Pipeline: застосовує обидва джерела
├── Models/
│   ├── PromoCode.php               # промокод (M2M → promo_code_product)
│   └── DiscountCard.php            # дисконтна картка
├── Contracts/
│   └── DiscountSource.php          # інтерфейс: getType(), getAmount(), getLabel(), isCompatibleWith()

resources/views/livewire/checkout/
├── checkout-form.blade.php         # секція промокоду + картки
└── payment/                        # (не відноситься сюди)

tests/Feature/Checkout/
├── DiscountActionsTest.php
├── CheckoutFormTest.php            # test_apply_promo_code, test_apply_discount_card, ...
└── PromoCodeProductRestrictionTest.php
```

## Моделі

### PromoCode

| Поле | Тип | Опис |
|------|-----|------|
| `code` | string | Унікальний код |
| `type` | enum('percent','fixed') | Тип знижки |
| `value` | decimal | Розмір знижки |
| `is_active` | bool | Активний |
| `products` | BelongsToMany | Обмеження по товарах (`promo_code_product`) |

```php
// Чи є обмеження по товарах
$promoCode->hasProductRestrictions(); // bool
// Чи діє на конкретний product_id
$promoCode->products->contains($productId);
```

### DiscountCard

| Поле | Тип | Опис |
|------|-----|------|
| `code` | string | Код картки |
| `phone` | string\|null | Телефон власника (для авто-визначення) |
| `type` | enum('percent','fixed') | Тип |
| `value` | decimal | Розмір знижки |
| `is_active` | bool | Активна |

## Actions

### ApplyPromoCode

```php
app(ApplyPromoCode::class)->handle(string $code, float $subtotal): ?array
// Повертає ['discount' => float, 'label' => string] або null
```

### ApplyDiscountCard

```php
app(ApplyDiscountCard::class)->handle(string $code, float $subtotal): ?array
// Повертає ['discount' => float, 'label' => string] або null
```

## CheckoutForm — властивості

| Властивість | Тип | Опис |
|---|---|---|
| `promoCodeInput` | string | Введений код промокоду |
| `promoApplied` | bool | Промокод застосовано |
| `promoDiscount` | float | Сума знижки промокоду |
| `cardInput` | string | Введений код картки |
| `cardApplied` | bool | Картку застосовано |
| `cardDiscount` | float | Сума знижки картки |

**Методи CheckoutForm:**

```php
applyPromoCode()   // валідує + застосовує промокод
removePromoCode()  // скидає
applyDiscountCard() // валідує + застосовує картку
removeDiscountCard() // скидає
```

## Конфіг

```php
// config/checkout.php
'discount_combinations' => [
    'promo_code'    => ['discount_card' => true],  // можна разом
    'discount_card' => ['promo_code' => true],
],
```

## Pipeline крок

`ApplyDiscounts` → викликає Actions → складає `DiscountBreakdown` в `CheckoutContext`:
- `context->discountBreakdown->promoCode`
- `context->discountBreakdown->discountCard`
- `context->discountTotal`

---

## Для адміністратора

### Як додати промокод

Через CMS (або Tinker):

```php
PromoCode::create([
    'code'      => 'SALE10',
    'type'      => 'percent',   // або 'fixed'
    'value'     => 10,          // 10% або 10 грн
    'is_active' => true,
]);
```

**Обмеження по товарах** — якщо промокод має діяти лише на певні товари:

```php
$promo->products()->attach([1, 2, 3]); // ID товарів
```

Якщо прив'язок немає — діє на всі товари.

### Як додати дисконтну картку

```php
DiscountCard::create([
    'code'      => 'GOLD001',
    'type'      => 'percent',
    'value'     => 5,
    'phone'     => '+380971234567', // для авто-визначення по телефону
    'is_active' => true,
]);
```

### Що показується покупцю

- Поле введення промокоду — завжди видно
- Поле введення картки — завжди видно
- Після застосування — рядок зі знижкою в підсумку

### Розширення

**Новий тип знижки** — додати клас що реалізує `DiscountSource`, зареєструвати в `ApplyDiscounts`.

**Заблокувати комбінацію** — змінити `discount_combinations` в конфізі:
```php
'promo_code' => ['discount_card' => false],
```

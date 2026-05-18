# Checkout — Реєстрація + Авто-картка (Phase 4.10)

> Статус: реалізовано | Сесія 40

## Що є

- **RegisterUser** — автоматична реєстрація гостя після успішного замовлення
- **AutoDetectDiscountCard** — пошук дисконтної картки по введеному номеру телефону

## Структура файлів

```
app/
├── Actions/Checkout/
│   ├── RegisterUser.php            # реєстрація гостя + email з паролем у черзі
│   └── AutoDetectDiscountCard.php  # пошук DiscountCard по phone з кешем

tests/Feature/Checkout/
├── RegisterUserTest.php
└── AutoDetectDiscountCardTest.php
```

## RegisterUser

**Запускається** з `CreateOrder` (PipelineStep) — після коміту транзакції, якщо `$context->registerMe = true` і покупець — гість.

```php
app(RegisterUser::class)->handle(Order $order): void
```

**Логіка:**
1. Перевіряє `$order->register_me === true` і `$order->user_id === null`
2. Пошук існуючого User по email — якщо є, не реєструє
3. Генерує випадковий пароль (32 символи plain string — модель User має `hashed` cast, bcrypt застосовується автоматично)
4. Створює User
5. Прив'язує `$order->user_id`
6. Відправляє `RegisteredMail` у чергу (`Mail::queue`)

> **Критично**: НЕ передавати `bcrypt($password)` — модель сама хешує через cast. Подвійне хешування ламає авторизацію.

**Поля, що беруться з Order:**
- `name` → `firstName` + `lastName`
- `phone`
- `email` (якщо пустий — `phone@noemail.local`)

## AutoDetectDiscountCard

Викликається в `CheckoutForm::updatedPhone()` — коли покупець вводить телефон.

```php
app(AutoDetectDiscountCard::class)->handle(string $phone): ?DiscountCard
```

**Нормалізація телефону** — перевіряє 5 форматів:
- `380XXXXXXXXX`, `+380XXXXXXXXX`, `380XXXXXXXXX`, `0XXXXXXXXX`, `XXXXXXXXX`

**Кешування:**
```php
// Ключ: discount_card.phone.{normalized_phone}
// TTL: 300 секунд
// Sentinel: int(0) = картка не знайдена (уникає повторних запитів для несуч. телефонів)
Cache::remember("discount_card.phone.{$clean}", 300, fn() => (int) DiscountCard::...->value('id'));
```

## CheckoutForm — властивості

| Властивість | Тип | Опис |
|---|---|---|
| `registerMe` | bool | Чи реєструвати гостя (default: true) |
| `autoDetectedCard` | ?array | Дані авто-визначеної картки `{id, code, value, type}` |

**Методи:**
```php
updatedPhone()           // викликає AutoDetectDiscountCard, зберігає в autoDetectedCard
applyAutoDetectedCard()  // застосовує avto-визначену картку
```

**Blade:**
```blade
@guest
    <input wire:model="registerMe" type="checkbox"> Зареєструватись
@endguest

@if($autoDetectedCard)
    <div>Знайдено картку {{$autoDetectedCard['code']}} — знижка {{$autoDetectedCard['value']}}%</div>
    <button wire:click="applyAutoDetectedCard">Застосувати</button>
@endif
```

## CreateOrder PipelineStep

```php
// Після DB::transaction — ПОЗА транзакцією (щоб не відправляти email при rollback)
DB::transaction(function() use ($context) {
    // ... створення Order, OrderProducts, pivot таблиць ...
});

// Після транзакції:
app(RegisterUser::class)->handle($context->order);
```

---

## Для адміністратора

### Як увімкнути/вимкнути авто-реєстрацію

За замовчуванням `registerMe = true` — чекбокс показується гостю і він може відмовитись.

Для авторизованих покупців — реєстрація не відбувається незалежно від налаштувань.

### Що отримує покупець після реєстрації

Email `RegisteredMail` з:
- Логін (email)
- Пароль (plain, генерований)
- Посилання для входу

### Як налаштувати авто-визначення дисконтної картки

Вказати телефон при створенні `DiscountCard`:
```php
DiscountCard::create([
    'code'      => 'CARD001',
    'phone'     => '+380971234567',
    'type'      => 'percent',
    'value'     => 5,
    'is_active' => true,
]);
```

Коли покупець вводить цей номер у форму — картка визначається автоматично.
Кеш живе 5 хвилин (`Cache::remember` з TTL 300s).

### Очистити кеш авто-визначення

```bash
php artisan cache:forget discount_card.phone.380971234567
# або очистити весь кеш:
php artisan cache:clear
```

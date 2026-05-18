# Checkout — Cart Guard & Order Safety (Phase 4.11)

> Статус: реалізовано | Сесія 44

## Що є

- **Редирект при порожньому кошику** — mount, removeCartItem, updateCartQty
- **CartGuard** — перевірка актуальності цін і наявності товарів перед оформленням
- **Ідемпотентність** — захист від подвійного відправлення форми
- **UI** — preloader, блокування кнопки, Alpine-модал при виявленні проблем

## Структура файлів

```
app/
├── Services/Checkout/CartGuard.php            # check(Collection): array
├── Livewire/Checkout/CheckoutForm.php         # mount redirect, runCartGuard, placeOrder

resources/views/livewire/checkout/
└── checkout-form.blade.php                    # preloader, кнопка, @script, Alpine modal

tests/Feature/Checkout/
├── CartGuardTest.php
├── CheckoutEmptyCartTest.php
└── CheckoutIdempotencyTest.php
```

## CartGuard

```php
namespace App\Services\Checkout;

class CartGuard
{
    public function check(Collection $cartItems): array
    // Повертає:
    // [
    //   'out_of_stock'  => [['rowId', 'id', 'name'], ...],
    //   'price_changed' => [['rowId', 'id', 'name', 'oldPrice', 'newPrice'], ...],
    // ]
}
```

**Логіка перевірки:**
1. Зчитує Product-и з БД за `id` з CartItems (один запит, `whereIn`)
2. Якщо Product не знайдено або `is_active=false` або `quantity<=0` → `out_of_stock`
3. Якщо `abs(product.price - cart.price) > 0.01` → `price_changed`

> CartItems з fake product_id (не існують у БД) → потрапляють в `out_of_stock`.

## CheckoutForm — властивості

| Властивість | Тип | Опис |
|---|---|---|
| `idempotencyKey` | string | UUID, генерується в mount() |
| `cartHasBlockingItems` | bool | true якщо є out_of_stock (блокує кнопку) |
| `cartGuardResult` | array | `{out_of_stock: [...], price_changed: [...]}` |

## Порожній кошик — редирект

**mount():**
```php
if ($this->cartCount === 0) {
    $this->redirect(route('home'), navigate: false);
    return;
}
```

**removeCartItem() / updateCartQty():**
```php
Cart::remove($rowId); // або update
$this->runCartGuard();
if ($this->cartCount === 0) {
    $this->redirect(route('home'), navigate: false);
}
```

## placeOrder() — порядок захистів

```
1. Ідемпотентність      → Cache::has('checkout:idem:{key}') → dispatch('notify') + return
2. Порожній кошик       → dispatch('notify') + return
3. Validate()           → assertHasErrors() → стандартні помилки форми
4. CartGuard            → check() → blocking → dispatch('cart-guard-blocking') + return
5. CartGuard warn       → price_changed → dispatch('notify:warn') (не блокує)
6. Build CheckoutContext → pipeline → redirect
```

> CartGuard виконується ПІСЛЯ валідації — щоб тести валідації з фейковими ID не ламались.

## Ідемпотентність

```php
// config/checkout.php
'idempotency' => [
    'enabled' => true,
    'ttl'     => 30,   // секунди — вікно захисту від дублів
],
```

**Ключ** генерується в `mount()`: `Str::uuid()`.
При кожному успішному `placeOrder()` ключ не оновлюється — повторний click у вікні TTL блокується.
Після успішного замовлення — redirect на success, ключ більше не актуальний.

## CartGuard конфіг

```php
// config/checkout.php
'cart_guard' => [
    'enabled'               => true,
    'block_on_out_of_stock' => true,  // true = кнопка недоступна + модал
    'warn_on_price_change'  => true,  // true = попередження (не блокує)
],
```

## UI — Blade

**Кнопка:**
```blade
<button
    wire:click="placeOrder"
    :disabled="$wire.cartHasBlockingItems || $wire.cartCount === 0"
    ...>
```

**Preloader:**
```blade
<div wire:loading wire:target="placeOrder" class="fixed inset-0 ...">
    {{-- SVG spinner --}}
</div>
```

**Alpine Cart Guard модал:**
```blade
<div x-data="cartGuardModal()" x-on:cart-guard-blocking.window="open($event.detail.items)">
    {{-- Список недоступних товарів, кнопка "Оновити кошик" --}}
</div>

@script
<script>
    Alpine.data('cartGuardModal', () => ({
        show: false,
        items: [],
        open(items) { this.items = items; this.show = true; },
        close() { this.show = false; },
    }));

    // Watcher: оновлює cartHasBlockingItems при зміні cartGuardResult
    $watch('$wire.cartGuardResult', (result) => {
        if (result.out_of_stock.length > 0) {
            $dispatch('cart-guard-blocking', { items: result.out_of_stock });
        }
    });
</script>
@endscript
```

---

## Для адміністратора

### Що означає "кнопка заблокована"

Якщо покупець бачить неактивну кнопку "Оформити замовлення":
- Один або більше товарів у кошику стали недоступними
- Або кошик порожній

**Рішення для покупця**: видалити недоступний товар з кошика або повернутися в каталог.

### Як увімкнути/вимкнути CartGuard

```php
// config/checkout.php
'cart_guard' => [
    'enabled'               => true,
    'block_on_out_of_stock' => true,  // false = не блокує кнопку (тільки попереджає)
    'warn_on_price_change'  => true,  // false = не попереджати про зміну ціни
],
```

### Як вимкнути захист від подвійного замовлення

```php
'idempotency' => [
    'enabled' => false,
],
```

Або збільшити TTL якщо замовлення обробляється довго:
```php
'idempotency' => [
    'enabled' => true,
    'ttl'     => 60,  // 60 секунд
],
```

### Де дивитись логи помилок

`storage/logs/checkout/checkout.log` — логуються помилки Pipeline + CartGuard events.

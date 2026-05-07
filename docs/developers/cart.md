# Кошик (Cart)

## Архітектура

```
app/
├── Actions/Cart/          — тонкі оркестратори (AddToCart, Remove, Update, AddMulti)
├── Cart/
│   ├── CartContext.php    — mutable DTO для Pipeline
│   ├── Pipes/             — ValidateProduct → CheckAvailability → PerformAdd
│   └── Contracts/CartPipeInterface.php
├── Contracts/
│   └── PricingStrategy.php  — інтерфейс цінової стратегії
├── DTO/CartResult.php
├── Events/Cart/           — CartItemAdded / Updated / Removed
├── Http/
│   ├── Controllers/CartController.php
│   └── Middleware/RecoverBasketFromCookies.php
├── Jobs/StoreUnfinishedBasket.php  (queue: low)
├── Listeners/Cart/PersistUnfinishedBasket.php
├── Livewire/Cart/
│   ├── Count.php          — лічильник у хедері
│   └── Drawer.php         — slide-in панель (подія: open-cart-drawer)
├── Models/
│   ├── UnfinishedBasket.php
│   └── UnfinishedBasketsProducts.php
├── Services/
│   ├── CartService.php
│   ├── Pricing/DefaultPricingStrategy.php
│   └── UnfinishedBasketService.php
└── ValueObjects/
    ├── Price.php           — bcmath, immutable
    ├── Money.php           — Price + ISO currency
    └── PriceTier.php       — оптовий рівень (min_qty + rate)
```

## Додавання товару в кошик

### JS → PHP

```js
// [data-js-add-to-cart] клік → CartHandler._add() → POST /cart/add/{id}
// Після успіху: Livewire.dispatch('open-cart-drawer')
```

### PHP Pipeline

```
AddToCartAction::handle(Product, qty, options)
  → CartService::addThroughPipeline()
    → ValidateProductPipe
    → CheckAvailabilityPipe
    → PerformAddPipe
  → dispatch(CartItemAdded)
    → PersistUnfinishedBasket listener
      → StoreUnfinishedBasket::dispatchSync() [sync — cookie вимагає HTTP-контексту]
```

Додати нову бізнес-логіку → новий Pipe в `config/cart.php`, не чіпати Actions.

## Drawer (slide-in кошик)

- Livewire: `App\Livewire\Cart\Drawer`
- View: `resources/views/livewire/cart/drawer.blade.php`
- Компоненти: `<x-cart.item>`, `<x-cart.summary>`
- Відкриття: `Livewire.dispatch('open-cart-drawer')`
- Відновлення цін при відкритті: `CartService::updateAvailabilityAndPrices()`

```blade
{{-- У layouts/shop.blade.php --}}
<livewire:cart.drawer />
```

## Blade-компоненти

### `<x-cart.item :item="$item" :product="$product" />`

- Alpine `cartStepper` — debounce 400ms → PATCH `/cart/update/{rowId}`
- `qty` НЕ в `x-data`, тільки `data-qty` + MutationObserver (захист від Livewire morph)
- Оптова ціна — закоментований блок, увімкнути розкоментуванням

### `<x-cart.summary :total="$total" :subtotal="$subtotal" />`

- Підсумок + кнопки "Оформити" / "Продовжити покупки"

## Валюта

```php
// Singleton, читає setting('currency') + setting('currency_code'), кешує 1h
$currency = app(CurrencyService::class);
$currency->symbol();              // ₴
$currency->code();                // UAH
$currency->format(1234.5);       // 1 234,50
$currency->formatWithSymbol(100); // 100 ₴
$currency->flushCache();          // після зміни Setting
```

В шаблонах — без змін: `@money($price) {{ setting('currency') }}`

## Цінова стратегія (PricingStrategy)

Інтерфейс `App\Contracts\PricingStrategy` прив'язаний до `DefaultPricingStrategy` в `AppServiceProvider`.

```php
// Щоб підмінити стратегію (наприклад, групові ціни в майбутньому):
$this->app->bind(PricingStrategy::class, GroupBasedPricingStrategy::class);
```

`DefaultPricingStrategy` читає `product->price` / `product->price_old` напряму.

## Оптові ціни (PriceTier)

Зберігаються в `categories.wholesale_tiers` (JSON):

```json
[
  {"min_qty": 10, "rate": 0.95},
  {"min_qty": 50, "rate": 0.90}
]
```

```php
// Отримання тирів категорії
$category->getWholesaleTiers(); // Collection<PriceTier>

// Ціни товару по рівнях: [10 => 950.0, 50 => 900.0]
$product->getWholesalePrices();

// PriceTier
$tier->minQty;            // 10
$tier->priceFor(1000.0);  // 950.0
$tier->discountPercent(); // 5
```

**Увага:** `getWholesalePrices()` вимагає eager-load категорії. `category_id` вже входить у `cardFields`. У запитах каталогу додавати `->with('category:id,wholesale_tiers')`.

## Відновлення кинутого кошика

Middleware `RecoverBasketFromCookies` (web-група, після session):

- Перевіряє cookie `unfinished_basket`
- Якщо кошик порожній → відновлює з `unfinished_baskets_products`
- Результат "нема що відновлювати" кешується на 5хв (Redis) → один DB-запит на сесію
- Пропускає: admin/*, api/*, livewire/update, статичні файли

## Highload-нотатки

| Компонент | Рішення |
|---|---|
| `StoreUnfinishedBasket` | `dispatchSync()` навмисно — cookie встановлюється у тому ж запиті; async не має доступу до HTTP-відповіді |
| `RecoverBasketFromCookies` | Cache 5хв на cookie-hash — один DB-запит на сесію |
| `getWholesalePrices()` | eager-load `category:id,wholesale_tiers` в каталозі |
| `CurrencyService` | singleton + Cache 1h — нульові DB-запити під час роботи |
| `CartService::updateAvailabilityAndPrices()` | викликати тільки при відкритті Drawer, не на кожен рендер |

## GA4 Analytics

- `add_to_cart` — після `CartHandler._add()` + `dispatch(CartItemAdded)`
- `remove_from_cart` — через `cart-changed` event з `action: 'remove'`
- `view_cart` — `Drawer::dispatchViewCart()` при відкритті
- `item_id` — завжди `$product->getArticle()` (code або id), не `->id`

# Архітектура доставки (Phase 4.3b)

## Концепція

Єдина таблиця `delivery_warehouses` замість окремих таблиць на кожного кар'єра. Кожен запис зберігає `carrier_ref` — оригінальний ID від API кар'єра, необхідний для синхронізації з 1С.

## БД-таблиці

### `delivery_warehouses`
| Поле | Тип | Опис |
|------|-----|------|
| `carrier` | varchar(32) | `np` / `ukrposhta` / `justin` / `meest` / `rozetka` |
| `type` | varchar(32) | `branch` / `poshtamat` / `pvz` / null |
| `city_id` | FK → cities | |
| `carrier_ref` | varchar(64) | **Оригінальний ID від API** (UUID для НП, int для інших) — для 1С |
| `title` | json | `{"ua": "...", "ru": "..."}` |
| `address` | varchar | |
| `is_active` | boolean | |

Унікальний індекс: `(carrier, carrier_ref)`.

### `city_carrier_codes`
Прив'язка міста до ID кар'єра (для 1С):
| Поле | Опис |
|------|------|
| `city_id` | FK → cities |
| `carrier` | np / ukrposhta / ... |
| `ref` | Оригінальний ID міста у кар'єра |

### `orders`
Поле `delivery_warehouse_id` → FK → `delivery_warehouses`. Через нього 1С отримує `carrier_ref`.

## Моделі

**`DeliveryWarehouse`** — `app/Models/DeliveryWarehouse.php`
- Константи: `CARRIER_NP`, `CARRIER_UKRPOSHTA`, `CARRIER_JUSTIN`, `CARRIER_MEEST`, `CARRIER_ROZETKA`
- Константи типів: `TYPE_BRANCH`, `TYPE_POSHTAMAT`, `TYPE_PVZ`
- `SLUG_MAP` — маппінг slug доставки → `[carrier, type]`
- Скоупи: `active()`, `forDeliverySlug(string $slug)`, `forCity(int $cityId)`
- Статичні: `carrierForSlug(string $slug)`, `hasWarehouseSearch(string $slug)`

**`CityCarrierCode`** — `app/Models/CityCarrierCode.php`

**`City`** — релейшени `warehouses()`, `carrierCodes()`

## Контракти

### `CarrierAdapter` — `app/Contracts/CarrierAdapter.php`
```php
interface CarrierAdapter {
    public function carrier(): string;
    public function label(): string;
    public function syncCities(CityResolver $resolver, TranslatorAdapter $translator): int;
    public function syncWarehouses(TranslatorAdapter $translator): int;
}
```

### `TranslatorAdapter` — `app/Contracts/TranslatorAdapter.php`
За замовчуванням — `NullTranslatorAdapter` (ua-текст як ru, без реального перекладу). Підключається через DI:
```php
$this->app->bind(TranslatorAdapter::class, NullTranslatorAdapter::class);
```

## Сервіси

**`CarrierAdapterRegistry`** — `app/Services/Delivery/CarrierAdapterRegistry.php`  
Реєстр адаптерів. Реєструється в `DeliveryServiceProvider` з 5 адаптерами.

**`CityResolver`** — `app/Services/Delivery/CityResolver.php`  
`findOrCreate(string $name, string $carrier, string $carrierRef, TranslatorAdapter $translator)` — знаходить або створює місто + запис у `city_carrier_codes`.

## Адаптери

`app/Adapters/Delivery/`:
- `NovaPoshtaAdapter` — carrier: `np`
- `UkrposhtaAdapter` — carrier: `ukrposhta`
- `JustinAdapter` — carrier: `justin`
- `MeestAdapter` — carrier: `meest`
- `RozetkaAdapter` — carrier: `rozetka`
- `NullTranslatorAdapter` — реалізація `TranslatorAdapter` без перекладу

Скелети — `syncCities()` та `syncWarehouses()` повертають `0`. Реальна логіка API додається окремо по кожному кар'єру.

## Консольна команда

```bash
# Синхронізувати конкретний кар'єр
php artisan delivery:sync np

# Всі кар'єри
php artisan delivery:sync --all

# Тільки міста / тільки склади
php artisan delivery:sync np --cities-only
php artisan delivery:sync np --warehouses-only
```

## Livewire: DeliverySelector

`app/Livewire/Checkout/DeliverySelector.php`

Використовує `DeliveryWarehouse::scopeForDeliverySlug($slug)` для пошуку відділень. Після вибору dispatch-ить єдину подію:
```php
$this->dispatch('delivery-details-updated',
    cityId: $this->selectedCityId,
    deliveryWarehouseId: $this->selectedWarehouseId,
    address: $this->address,
    deliveryPickupPointId: $this->pickupPointId,
);
```

`CheckoutForm` слухає `#[On('delivery-details-updated')]` і зберігає `deliveryWarehouseId` → `CheckoutContext::$deliveryWarehouseId` → `orders.delivery_warehouse_id`.

## 1С-інтеграція

Для отримання оригінального ID відділення у кар'єра:
```php
$order->deliveryWarehouse->carrier_ref   // UUID/int від API кар'єра
$order->deliveryWarehouse->carrier       // 'np' | 'ukrposhta' | ...
$order->city->carrierCodes               // всі прив'язки міста до кар'єрів
```

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
| `meta` | json\|null | **Carrier-specific поля** (div. нижче) |

**`meta` — поля по кар'єрах:**
```jsonc
// НП
{"only_receiving_parcel": true, "max_weight": 30}

// Rozetka
{"can_give_out_tracks": true, "can_receive_tracks": false, "volume_weight": 30, "schedule": "..."}
```

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

## Сумісність з CMS-адмінкою (linecore-demo)

### Принцип: per-carrier сторінки через фільтр

Адмінка linecore-demo має окремі CMS-визначення для кожного кар'єра:
`NpWarehouse`, `UkrposhtaWarehouse`, `JustinWarehouse`, `MeestWarehouse`, `RozetkaWarehouses`.

У demo.loc це одна таблиця `delivery_warehouses`. Коли будемо будувати CMS для demo.loc — кожна сторінка кар'єра просто фільтрує `DeliveryWarehouse::where('carrier', 'np')` тощо.

### `Delivery.type` vs `Delivery.slug`

| linecore-demo `type` | demo.loc `slug` |
|---|---|
| `np` | `np_branch` |
| `np_pochtomat` | `np_poshtamat` |
| `np_address` | *(немає — адресна НП не виділена окремо)* |
| `pickup` | `pickup` |
| `ukrposhta` | `ukrposhta` |
| `justin` | `justin` |
| `meest` | `meest` |
| `rozetka` | `rozetka` |

У demo.loc використовується `slug` (замість `type`). При побудові CMS-визначення `Deliveries` для demo.loc — колонка `slug`.

### Міста

| linecore-demo | demo.loc |
|---|---|
| `np_cities` (NPCity) | `cities` + `city_carrier_codes` (carrier=np) |
| `cities` (City — Укрпошта/Justin/Meest) | `cities` + `city_carrier_codes` |
| `rozetka_cities` (RozetkaCity з полем `ref`) | `cities` + `city_carrier_codes` (carrier=rozetka, ref) |

CMS-сторінки міст у demo.loc будуть фільтрувати через `city_carrier_codes.carrier`.

### Специфічні поля кар'єрів → `meta`

| Кар'єр | linecore-demo поле | demo.loc `meta` ключ |
|---|---|---|
| НП | `pochtomat` | → `type = 'poshtamat'` (в основній схемі) |
| НП | `only_receiving_parcel` | `meta.only_receiving_parcel` |
| НП | `max_weight` | `meta.max_weight` |
| Rozetka | `ref` | → `carrier_ref` (в основній схемі) |
| Rozetka | `can_give_out_tracks` | `meta.can_give_out_tracks` |
| Rozetka | `can_receive_tracks` | `meta.can_receive_tracks` |
| Rozetka | `volume_weight` | `meta.volume_weight` |
| Rozetka | `schedule` | `meta.schedule` |

### DeliveryPickupPoints

Повністю сумісно: `delivery_pickup_points.delivery_id` + `city_id`. CMS-визначення `DeliveryPickupPoints` портується без змін.

### Checkout UI

`DeliverySelector` показує різний UI залежно від `deliverySlug` — аналогічно тому, як в linecore-demo checkout рендерить окремий шаблон для кожного `delivery.type`. Логіка збережена.

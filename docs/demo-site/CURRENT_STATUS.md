# Поточний статус demo.loc — читати на початку кожної сесії

**Оновлено:** 2026-05-02 (сесія 9)
**Прогрес:** Фази 0.5 + 0.6 + 0.6b + 0.6c + 1 + 2 + 3 + 3.7 + SEO + Каталог-оптимізація A+B+C + Баги + Пагінація SPA. Поточна: **Фаза 4 (не почата)**.

---

## ▶ Продовжити звідси (пріоритет зверху вниз)

1. **[ПРІОРИТЕТ] Фаза 4 — Корзина:** повна реалізація згідно `memory/cart_phase4_architecture.md`
   *(Баги каталогу зафіксовано, Фаза 0.6c завершена)*
   - Вивчення linecore-demo завершено в сесії 7
   - Ключове уточнення: репозиторії НЕ використовуємо — логіку DB напряму в сервіс/модель
   - Реалізацію НЕ починали — починати з нуля
   - Порядок: **міграції → моделі → DTO → сервіс → контролер → routes → JS → Livewire → views → layout**

2. **[ЗАПЛАНОВАНО] Phase 0.7 — i18n Routing + Smart Cache** (деталі в DEVELOPMENT_CHECKLIST.md)

---

## Зроблено в сесії 9 (2026-05-02)

### Phase 0.6c — Catalog Bug Fixes ✅
- **Bug 1 (фільтр скидав сортування):** `filter.js _navigate()` зберігає `?sort=` з `window.location`
- **Bug 2 (сортування скидало фільтри):** `sort.js` читає `window.location.pathname` при кліку; `SortBar` → `currentUrl()` (CMS helper, Referer fallback для Livewire)
- **Bug 3 (пагінація втрачала сортування):** `pagination.js` зберігає `?sort=`, перша сторінка без `?page=`
- `ProductList` — `#[On('sort-changed')]` + `resolveSortKey()`, скидає page
- `SortBar` — `#[On('sort-changed')]` active-стан; `href` для SEO-ботів, `data-js-sort` для JS
- `filter.js _bindPopstate()` — диспатчить `sort-changed` при back/forward
- **Ключовий урок:** в Livewire-компонентах `url()->current()` = `/livewire/update`; завжди `currentUrl()` з CMS-пакету

## Зроблено в сесії 8

### Phase 0.6 — Security Foundation ✅
- `BlockBotRequests` middleware — 204 ботам на службових ендпоінтах
- `ValidateLivewireMethod` middleware — захист від RCE/ін'єкцій через Livewire 3/4
- `Livewire::setUpdateRoute()` у `web.php` — обидва middleware на `/livewire/update`
- `security` log channel → `storage/logs/security/security-YYYY-MM-DD.log` (30 днів)

### Phase 0.6b — UI Foundation ✅
- **Tailwind config** — brand colors UI Kit: `brand #2563EB`, `ink #111827`, `surface #F5F6F8`
- **Inter font** у shop.blade.php (bunny.net)
- **Рішення по UI-стеку** (зафіксовано в пам'яті):
  - Flux — тільки auth/settings (вже є, 248+ вживань)
  - WireUI — НЕ встановлюємо (важкий для PageSpeed)
  - wire-elements/modal — НЕ сумісний з Livewire 4
  - Volt — не розвиваємо (scaffolding залишається)
  - Live/залежні списки — Alpine.data() + fetch

### ModalManager System ✅ (замінює wire-elements/modal, Livewire 4 native)
- `app/Livewire/Components/ModalManager.php` — wire-elements/modal-compatible API
  - `openModal` / `closeModal` / `closeModalWithEvents` events ✅
  - `modalMaxWidth()` static override ✅
  - Lazy: HTML модалки NOT в DOM до виклику ✅
  - TODO: Винести в пакет `linecore/modal`
- `app/Livewire/Concerns/IsModalForm.php` — trait замість `extends ModalComponent`
- `app/Livewire/Concerns/HasNotifications.php` — `$this->notify()`, `notifySuccess()` тощо
- `resources/views/livewire/components/modal-manager.blade.php`
- `resources/js/base/livewire-modal.js` — `[data-js-modal]` атрибутний підхід
- `resources/js/base/lazy-loading-img.js` — IntersectionObserver + MutationObserver

### Toast Notifications ✅ (замінює Web Component + окремий CSS)
- `resources/js/components/notification.js` — Alpine.js, Tailwind, без зовнішніх залежностей
- `resources/views/components/toast.blade.php` — `<x-toast />`
- Виклик: `$this->notifySuccess('...')` | `window.notify('success', '...')` | `Livewire.dispatch('notify', ...)`

### Документація ✅
- `docs/developers/modal-and-notifications.md` — повний гайд + таблиця міграції з wire-elements/modal

---

## Що вивчено (сесія 7, linecore-demo) — готово до портування Фази 4

### Джерела в linecore-demo
- `app/Services/Basket.php` — CartService (повертає JsonResponse — в demo.loc замінити на CartResult DTO)
- `app/Services/UnfinishedBasketService.php` — OK, але прибрати Repository
- `app/Jobs/StoreUnfinishedBasket.php` — OK (dispatchSync)
- `app/Models/UnfinishedBasket.php` — OK (без $revisionEnabled)
- `app/Models/UnfinishedBasketsProducts.php` — OK
- `app/Livewire/Cart/Count.php` — OK (з #[Lazy])
- `app/Livewire/Cart/Content.php` → в demo.loc буде `Cart\Sidebar` (slide-in, не modal)
- `resources/dev/js/base/basket.js` — стара версія; в demo.loc — data-attr-based

### Структура Actions (ПІДТВЕРДЖЕНО)
- `app/Actions/Cart/AddToCartAction::handle(Product, int, array): CartResult`
- `app/Actions/Cart/RemoveFromCartAction::handle(string): CartResult`
- `app/Actions/Cart/UpdateCartItemAction::handle(string, int): CartResult`
- `CartService` — утилітний: `availableForOrder`, `updateAvailabilityAndPrices` тощо
- `CartController` — тонкий, перетворює `CartResult → JsonResponse`

### Що НЕ портуємо в Phase 4
- `Addtocart` Livewire компонент — додавання через JS (`data-js-add-to-cart` + basket.js)
- `CartOpener` trait, Analytics, addMulti, buyOneClick, payparts
- Репозиторії — логіку напряму в сервіс/модель

### Product — методи для Phase 4
```php
getPrice(): float
getQuantity(): int  // повертає $this->quantity ?? 9999
isActiveForOrder(): bool  // $this->is_active && $this->getQuantity() > 0
getWholesalePrices(): ?array
getProductDataAnalitic(): array
```

---

## Відкриті питання / TODO

- ✅ Баги каталогу — фільтр/сорт/пагінація: зафіксовано в Phase 0.6c
- 🟡 Фільтри 3.7 — клік → URL → reload → відновлення (потребує браузерної перевірки)
- 🟡 Phase 0.7 — i18n routing (`mcamara/laravel-localization`) + smart cache (per-entity invalidation)
- 🟡 `linecore/modal` пакет — ModalManager + IsModalForm (після стабілізації)

---

## Правила розробки

- `$model->getUrl()` → URL моделей; `geturl()` → локалізовані URL; `route()` → API/named routes
- `currentUrl()` → ЗАВЖДИ замість `url()->current()` в Livewire-компонентах (Referer fallback!)
- `getQueryParam('p')` → замість `request()->query()` в Livewire (читає реальний `$_GET`)
- `parse_url($model->getUrl(), PHP_URL_PATH)` → тільки path для basePath, prefix
- `withPath(geturl(request()->path()))` — завжди для пагінатора (locale prefix!)
- `[data-js-paginator]` — wrapper пагінатора; `[data-js-product-grid]` — grid
- `__t()` — фронтенд; `__cms()` — тільки адмінка
- Alpine: NO inline JS → `Alpine.data()` в JS файлах; `data-config` замість `@json()` в `x-data`
- Vendor файли — НЕ копіювати в `app/`
- Портувати з linecore-demo, не вигадувати
- Репозиторії — НЕ використовуємо (логіку DB напряму в сервіс/модель)
- **Логи** — кожен канал у підпапку: `storage/logs/{channel}/{channel}.log`
- **Modal виклик** — `[data-js-modal]` атрибут у Blade; `IsModalForm` trait у компоненті
- **Toast** — `HasNotifications` trait → `$this->notifySuccess()`; JS → `window.notify()`
- **Дизайн** — Demo Shop.html + UI Kit.html в `linecore-demo/docs/demo-site/Demo shop (1)/`
- **НЕ SPA** — кожна сторінка = окремий Blade шаблон + Livewire

---

## Docker / Команди

```bash
# PHP/Artisan/Composer
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan ..."
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php /var/www/demo.loc/composer ..."

# npm — на хості
cd /mnt/DataM2/Job/Sites/Linecore/demo.loc && npm run build

# Тести
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan test --compact --filter=Catalog"
```

---

## Посилання

- Повний ТЗ: `DEVELOPMENT_CHECKLIST.md`
- Дизайн: `linecore-demo/docs/demo-site/Demo shop (1)/Demo Shop.html` + `UI Kit.html`
- Docs для розробників: `docs/developers/`
- TODO архітектура: `docs/TODO/`

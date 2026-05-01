# Demo.loc Розробка - Прогресс

**Дата оновлення:** 2026-05-02
**Версія:** Phase 3+ + Security + UI Foundation + Catalog Bug Fixes

> **Правила коду:** [`docs/code/CODE_RULES.md`](../code/CODE_RULES.md) | [`.junie/guidelines.md`](../../.junie/guidelines.md)
> **Команди:** `docker exec -it laradock-php-fpm-1 bash` (composer/artisan — тільки в контейнері)
> **Документація для розробників:** [`docs/developers/`](../developers/)
> **TODO архітектура:** [`docs/TODO/`](../TODO/)

---

## Phase 1 & 2: Основна архітектура ✅
- [x] Models (Product, Category, Brand, Characteristic, CharacteristicOption)
- [x] Migrations (12 таблиць)
- [x] Relationships & casts
- [x] Factories для всіх моделей
- [x] DatabaseSeeder (100 товарів, 8 категорій, 8 характеристик)

---

## Phase 3: Каталог з TypeSense ✅

### Архітектура & Сервіси ✅
- [x] TypeSenseService (пошук, фільтрація, сортування)
- [x] FacetService (кешовані фасети, Redis, 15 хв TTL)
- [x] OptionSearchService (нечіткий пошук опцій, Levenshtein, Redis індекс)
- [x] TypeSense Docker контейнер (27.0, port 8108)
- [x] Scout конфіг (config/scout.php)

### Контроллери & Маршрути ✅
- [x] CatalogController (web)
- [x] Api\v1\CatalogController (API)
- [x] `bootstrap/app.php` — `api: routes/api.php` підключено (виправлено 404)
- [x] Web routes (/catalog/{category})
- [x] API routes (/api/v1/catalog/*)

### Livewire Компоненти ✅
- [x] Catalog\Page (контейнер)
- [x] Catalog\Facets (фасети)
- [x] Catalog\ProductList (товари + пагінація)
- [x] Catalog\SortBar (сортування)

### Пагінація ✅
- [x] "Show more" — Alpine.js + `GET /api/v1/catalog/products` (без Livewire state)
- [x] Нумерована — `wire:click="setPage(N)"` (без `#[Url]`, зберігає фільтри)
- [x] `#[Computed]` на `products()` — не потрапляє в wire:snapshot
- [x] Конфіг через `data-config` + `$el` (не `@json()` в x-data — ламає Alpine)
- [x] `product-list-reset` browser event — скидає `extraProducts` після Livewire ре-рендеру
- [x] Прелоадер — overlay з `wire:loading` + `wire:target` на grid

### Шаблони ✅
- [x] catalog/index.blade.php
- [x] livewire/catalog/facets.blade.php
- [x] livewire/catalog/product-list.blade.php
- [x] livewire/catalog/sort-bar.blade.php

### Тестові Дані ✅
- [x] 150 товарів (100 base + 50 в cat 3 для тесту пагінації)
- [x] 8 категорій, 8 характеристик, 12 брендів
- [x] TypeSense реіндексовано

### Оптимізація для 1M+ ✅
- [x] Раздельне кешування: товари (TypeSense) vs фасети (Redis)
- [x] Precise cache tags (category_ID)
- [x] Atomic locks від cache stampede
- [x] Chunk-based indexing

---

## Phase 0.6: Security Foundation ✅ (2026-05-01)

- [x] `BlockBotRequests` middleware — 204 на службових ендпоінтах для ботів
- [x] `ValidateLivewireMethod` middleware — захист від RCE/ін'єкцій (Livewire 4)
- [x] `Livewire::setUpdateRoute()` в `web.php` — обидва middleware на `/livewire/update`
- [x] `security` log channel → `storage/logs/security/security-YYYY-MM-DD.log` (30 днів)

## Phase 0.6c: Catalog Bug Fixes ✅ (2026-05-02)

- [x] **Bug: фільтр скидав сортування** — `filter.js _navigate()` тепер зберігає `?sort=` з `window.location`
- [x] **Bug: сортування скидало фільтри** — `sort.js` читає `window.location.pathname` (актуальний після pushState), `SortBar` використовує `currentUrl()` хелпер з CMS-пакету (Referer fallback для Livewire-запитів)
- [x] **Bug: пагінація втрачала сортування** — `pagination.js` зберігає `?sort=`, перша сторінка без `?page=`
- [x] `ProductList` — `#[On('sort-changed')]` handler, скидає page при зміні сорту
- [x] `SortBar` — `#[On('sort-changed')]` для оновлення active-стану; `href` коректний для SEO-ботів
- [x] `filter.js _bindPopstate()` — також диспатчить `sort-changed` при back/forward

## Phase 0.6b: UI Foundation ✅ (2026-05-01)

- [x] Tailwind config — brand/ink/surface кольори з UI Kit.html (`#2563EB`, `#111827`, `#F5F6F8`)
- [x] Inter font (bunny.net) у shop.blade.php
- [x] Рішення по стеку: Flux (auth only), без WireUI, без wire-elements/modal (Livewire 4 incompatible)

### ModalManager System (замінює wire-elements/modal, Livewire 4 native) ✅
- [x] `app/Livewire/Components/ModalManager.php` — compatible API: openModal / closeModal / closeModalWithEvents / modalMaxWidth
- [x] `app/Livewire/Concerns/IsModalForm.php` — trait замість `extends ModalComponent`
- [x] `app/Livewire/Concerns/HasNotifications.php` — notifySuccess/Error/Info/Warning
- [x] `resources/views/livewire/components/modal-manager.blade.php` — lazy render (NOT in DOM until called)
- [x] `resources/js/base/livewire-modal.js` — `[data-js-modal]` атрибутний виклик
- [x] `docs/developers/modal-and-notifications.md` — гайд + таблиця міграції

### Toast Notifications (замінює Web Component) ✅
- [x] `resources/js/components/notification.js` — Alpine.js, Tailwind, нуль залежностей
- [x] `resources/views/components/toast.blade.php` — `<x-toast />`

### Lazy Images ✅
- [x] `resources/js/base/lazy-loading-img.js` — IntersectionObserver + MutationObserver, Livewire morph aware

---

## Phase 0.7: i18n Routing + Smart Cache 🔲

### i18n Routing (порт з linecore-demo + Velosipedi fix)
- [ ] `composer require mcamara/laravel-localization`
- [ ] `app/Http/PatchedRequest.php` + `LaravelLocalizationServiceProvider` (fix getBaseUrl='/')
- [ ] `config/laravellocalization.php` — налаштування локалей (uk, ru, en…)
- [ ] `web.php` — `Route::group(['prefix' => LaravelLocalization::setLocale(), ...], ...)`
- [ ] `LocaleCookieRedirect` middleware у web-групу
- [ ] `CheckValidLanguage` global middleware
- [ ] `routes/api_localized.php` — API ендпоінти з підтримкою поточної локалі
- [ ] Оновити `geturl()`, `$model->getUrl()` — перевірити сумісність з locale prefix

### Smart Cache (per-entity invalidation)
- [ ] Аналіз поточного кешу: `FacetService`, `TypeSenseService`, `ProductList`
- [ ] Product/Category Observers → інвалідація тільки конкретних ключів (не весь Redis)
- [ ] Cache-key strategy: `product:{id}`, `facets:cat:{id}`, `category:{id}` з TTL
- [ ] Оцінити `lacodix/laravel-model-filter` — Model-level фільтри замість raw queries
- [ ] Оцінити `mr-punyapal/laravel-extended-relationships` — зменшення кількості запитів у зв'язках
- [ ] Оцінити `iazaran/smart-cache` — LRU/smart invalidation поверх Redis

---

## Phase 0.7+: Pipeline & Events (для Phase 6 — Checkout) 🔲

- [ ] Оцінити `michael-rubel/laravel-enhanced-pipeline` — для checkout flow (Pipeline з rollback)
- [ ] Оцінити `josepostiga/attribute-events` — field-level events на Product/Category моделях

---

## Phase 3+: CMS Сумісність ✅

### CMS Stub Міграції ✅ (23 міграції — всі DONE)
- [x] `create_cms_translations_tables` — `translations_phrases`, `translations`
- [x] `add_cms_columns_to_users_table` — `first_name`, `last_name`, `image`, `permissions`, `last_login`
- [x] `create_cms_auth_tables` — `roles`, `role_users`, `activations`, `persistences`, `reminders`, `throttle`
- [x] `create_cms_content_tables` — `settings`, `tb_tree` (url, ico, css, virtual cols, triggers), `revisions`
- [x] `create_cms_seo_languages_tables` — `seo` (повна схема), `languages`
- [x] `create_cms_admin_translations_tables` — `translations_phrases_cms`, `translations_cms`

### Документація ✅
- [x] `docs/old_cms_refactor/` — аналіз проблем CMS (4 файли)
- [x] `docs/code/CODE_RULES.md` — правила коду
- [x] `.junie/guidelines.md` — правила для AI/Junie
- [x] `docs/developers/catalog.md` — документація каталогу для розробників
- [x] `docs/TODO/pagination-filter-architecture.md` — архітектура уніфікованої пагінації

---

## Phase 3.7: SEO фільтрів 🔲

- [ ] `#[Url]` атрибути для стану фільтрів в URL
- [ ] Canonical URL для фільтрованих сторінок
- [ ] `noindex` для малоцінних комбінацій
- [ ] Sitemap для фільтрів з достатньою кількістю товарів
- [ ] OG теги для категорій

---

## Наступні фази

### Phase 4: Кошик 🔲
- [ ] Підключити `linecore/shoppingcart` пакет
- [ ] Livewire компонент кошика
- [ ] Mini-cart у header
- [ ] Сторінка кошика

### Phase 5: Сторінка товару 🔲
- [ ] ProductController + маршрут `/products/{slug}`
- [ ] Галерея, характеристики, SEO

### Phase 6: Оформлення замовлення 🔲

---

## API Endpoints

```
GET  /catalog/{category}                         — сторінка каталогу (web)
GET  /api/v1/catalog/products                    — товари (Show more)
GET  /api/v1/catalog/{category}/facets           — фасети
GET  /api/v1/catalog/{category}/facets/{id}/expanded
GET  /api/v1/catalog/options/{id}/search
GET  /api/v1/catalog/options/{id}/range-stats
```

---

## Примітки

**Видалено / не використовується:**
- `seo_model_h1` — видалено з seo міграції
- `setting_select` — видалено в CMS v3
- `external_id`, `analogs` — не потрібні для demo.loc

**Важливі правила:**
- `@json()` в `x-data=""` — ЗАБОРОНЕНО (ламає Alpine при фільтрах з `"`)
- Конфіг Alpine — через `data-config` + `$el`
- `composer`/`artisan` — тільки в контейнері `laradock-php-fpm-1`

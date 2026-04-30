# Поточний статус demo.loc — читати на початку кожної сесії

**Оновлено:** 2026-04-30 (сесія 5)  
**Прогрес:** Фази 0.5 + 1 + 2 + 3 + 3.7 + SEO + Каталог-оптимізація Blocks A + B + C + Баги + Пагінація SPA. Поточна: **Фаза 4**.

---

## ▶ Продовжити звідси (пріоритет зверху вниз)

1. **[ПРІОРИТЕТ] Фаза 4 — Корзина:** `CartSidebar` + slide-in в shop layout

2. **[ПІСЛЯ 4] Фаза 5 — Сторінка товару**

---

## Що зроблено в останній сесії (2026-04-30, сесія 5)

**Блок C — оптимізація Livewire:**
- ✅ `Facets::enrichWithUrls` видалено — URL більше не генеруються в PHP
- ✅ `facets()` повертає сирі дані з `FacetService` без збагачення URL-ами
- ✅ `facets.blade.php` — `data-char-slug`/`data-opt-slug` на checkbox замість `data-url`; `data-base-path` на root div
- ✅ `filter.js` — `_buildOptionUrl(charSlug, optSlug)` будує URL з pathname + basePath в JS; `_resolveInputUrl()` уніфікує старий і новий формат
- ✅ `filter-range.js` — debounce 600ms на Livewire dispatch (URL оновлюється одразу, запит — через 600ms)
- ✅ Тести оновлено: `test_facets_renders_option_data_attributes`, `test_facets_renders_checkbox_inputs`
- ✅ 42/42 тести pass, npm run build OK

## Що зроблено раніше (сесія 4, 2026-04-30)

**Баги виправлено:**
- ✅ SEO canonical — `$page->getUrl()` замість `currentUrl()` (фільтр-сторінки мали неправильний canonical)
- ✅ Range slider morph — `_morphHookCleanup` (хук більше не накопичується), перевірка `el.contains(s.container)`
- ✅ `Facets::onFilterChanged` — приймає `page` параметр, передає в `filtersUpdated`
- ✅ `ProductList::applyFilters` — приймає `page` параметр (не завжди reset до 1)
- ✅ `_bindPopstate` — передає `page` з URL в `filter-changed` (відновлення page при back/forward)

**Пагінація без перезавантаження:**
- ✅ `resources/js/catalog/pagination.js` — generic перехватчик кліків на `[data-js-paginator] a`
- ✅ `ProductList` — новий `#[On('page-changed')] setPage(int $page)` обробник
- ✅ `catalog/pagination.blade.php` — `id="js-pagination"` → `data-js-paginator` (без хардкоду)
- ✅ `load-more.js` — `getElementById` → `querySelector('[data-js-paginator]')`
- ✅ `product-list.blade.php` — додано `data-js-product-grid` атрибут
- ✅ `app.js` — імпорт `pagination.js`

**Без хардкоду:**
- ✅ `CatalogController` — `basePath` через `parse_url($category->getUrl(), PHP_URL_PATH)`
- ✅ `Facets` mount + `onFilterChanged` — аналогічно через `getUrl()`
- ✅ `product-list.blade.php` API URL — `route('api.v1.catalog.products-html', $category)`
- ✅ `routes/api.php` — додано name `api.v1.catalog.products-html`

**Тести:** 15/15 pass. `npm run build` — OK.

---

## Що зроблено раніше

**Блок B (сесія 3, 2026-04-29):**
- ✅ `ProductList::products()` → `LengthAwarePaginator`
- ✅ `withPath(geturl(request()->path()))` — locale-aware pagination
- ✅ Custom paginator view `catalog.pagination`
- ✅ `SortBar` — `<a href="?sort=...">` (не wire:change)
- ✅ `ResolvesSort` trait
- ✅ API `getProductsHtml()` — fragments для load-more
- ✅ `load-more.js` — DOMParser + фрагменти
- ✅ `RedirectSEO` middleware — 301 для `?page=1`, `www.`, trailing slash
- ✅ `remove-first-page-from-url.js`

**Блок A + каталог-оптимізація (сесія 2, 2026-04-29):**
- ✅ `CatalogDemoSeeder` — 220 товарів, 8 категорій
- ✅ `filter-group.js`, сортування через URL
- ✅ `SlugUrlFieldTrait` + `Product::getUrl()`

**Фази 0.5—3.7:**
- ✅ TypeSense + Scout, каталог 200 OK
- ✅ SEO-інфраструктура: HasSeo, Seo, SeoComposer
- ✅ PATH-based SEO фільтри: FilterUrlService, FacetService, filter.js

---

## Контекст проекту

**demo.loc = порт linecore-demo на нову архітектуру:**
- Laravel 12 + Livewire 3 + Alpine.js + TypeSense
- SEO filter URLs в path (`/char=val/`)
- Нативний JS + history API
- URL: `$model->getUrl()`, звичайні: `geturl()`, API: `route()`

---

## Критичні правила

- `$model->getUrl()` → URL моделей; `geturl()` → локалізовані URL; `route()` → API/named routes
- `parse_url($model->getUrl(), PHP_URL_PATH)` → тільки path для basePath, prefix
- `withPath(geturl(request()->path()))` — завжди для пагінатора (locale prefix!)
- `page_base` від клієнта — завжди валідувати `str_starts_with`
- `[data-js-paginator]` — wrapper пагінатора; `[data-js-product-grid]` — grid
- `__t()` — фронтенд; `__cms()` — тільки адмінка
- Alpine: NO inline JS → `Alpine.data()` в JS файлах; `data-config` замість `@json()` в `x-data`
- Vendor файли — НЕ копіювати в `app/`
- Портувати з linecore-demo, не вигадувати

---

## Відкриті баги

- 🟡 Фільтри 3.7 — клік → URL; reload → відновлення; "Скинути" (потребує браузерної перевірки)

---

## TODO (зафіксовано в docs/TODO/)

- `models-traits-basemodel.md` — BaseModel + трейти (Sluggable, PrepareModelFields)
- `trait-slughurl-refactor.md` — per-instance кешування getUrl()

---

## Docker / Команди

```bash
# PHP/Artisan/Composer
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan ..."

# npm — на хості
cd /mnt/DataM2/Job/Sites/Linecore/demo.loc && npm run build

# Тести
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan test --compact --filter=Catalog"

# Після migrate:fresh
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan migrate:fresh --seed && php artisan scout:import 'App\Models\Product'"
```

---

## Посилання

- Повний ТЗ: `DEVELOPMENT_CHECKLIST.md`
- Правила: `RULES.md`
- TODO: `docs/TODO/`
- Skills: `.agents/skills/README.md`

---
name: catalog-seo-filter-urls
description: Архитектура SEO-friendly фильтров каталога для demo.loc. Применяйте при реализации фильтрации товаров, range-слайдеров, URL-менеджмента фильтров. Не используйте для других страниц.
metadata:
  author: linecore
  version: "1.0"
  package: demo.loc
  updated: "2026-04-28"
---

# SEO-friendly фильтры каталога — demo.loc

## Контекст и референсы

- **Источник архитектуры:** `/mnt/DataM2/Job/Sites/Linecore/ServiceMarket/project/gitlab/service-market/resources/dev/js/catalog/filter.js`
- **Blade компоненты:** `ServiceMarket/.../resources/views/components/filter-group-checkbox.blade.php` + `filter-group-range.blade.php`
- **PHP архитектура:** `ServiceMarket/.../app/Services/Filters/AbstractFilter.php`
- **Стек demo.loc:** Laravel 12 + Livewire 3 + TypeSense (вместо ES)

## Ключевой принцип (ServiceMarket)

Фильтры — это НЕ Livewire wire:model, НЕ GET-параметры.

```
Клик на опцию
  → JS читает data-url атрибут
  → history.pushState(newUrl)
  → Livewire.dispatch('filter-changed', {path: window.location.pathname})
  → Facets.php #[On('filter-changed')] читает path, парсит, обновляет ProductList
```

Fильтры в URL — только для SEO и `history.pushState`. **JS — источник изменения URL. Livewire — источник истины для состояния после получения события.**

---

## URL формат

```
/catalog/{category-slug}/{char-slug1}={opt-slug1},{opt-slug2}/{char-slug2}={opt-slug3}/price={min}-{max}
```

Правила:
- Каждый сегмент: `{char-slug}={opt-slug1},{opt-slug2}` — только slugs (не IDs)
- Сегменты отсортированы по ключу (ksort) для канонических URL
- Цена: `/price={min}-{max}` (path сегмент, НЕ GET-параметр)
- Range характеристика: `/{char-slug}={min}-{max}` (определяется по `is_range_type=1`)
- Пагинация: `?page=N` (GET-параметр — единственный разрешенный GET)

Примеры:
```
/catalog/telefony/                                                  # без фильтров
/catalog/telefony/brend=samsung,apple/                             # бренд (мультиселект)
/catalog/telefony/brend=samsung/kolir=chornyi/                     # два фильтра
/catalog/telefony/brend=samsung/price=5000-15000/                  # с ценой
/catalog/telefony/brend=samsung/ram=4-16/                          # range-характеристика
/catalog/telefony/brend=samsung/price=5000-15000/?page=2           # пагинация — GET
```

---

## Роутинг (оптимальная архитектура)

```php
// routes/web.php

// Каталог: category + wildcard для filter-сегментов
Route::get('/catalog/{category}/{filters?}', [CatalogController::class, 'show'])
    ->where(['category' => '[^/]+', 'filters' => '.*'])
    ->name('catalog.show');

// Товар: /product/{slug} — явний сегмент, без конфліктів
Route::get('/product/{product:slug}', [ProductController::class, 'show'])
    ->name('product.show');

// Fallback одноуровневий — для клієнтів без сегментів (СТАВИТИ ОСТАННІМ)
// CatalogController::routeSlug() спочатку шукає category, потім product
Route::get('/{slug}', [CatalogController::class, 'routeSlug'])
    ->where('slug', '[^/]+')
    ->name('slug.route');
```

**Архітектура роутингу:**
- Primary: `/product/{slug}` — явний сегмент, без конфліктів з filter-сегментами
- Fallback: `/{slug}` — для клієнтів що хочуть одноуровневі URL (ставити ОСТАННІМ)
- В шаблонах: `route('product.show', $product)` → `/product/{slug}`
- `routeSlug($slug)`: спочатку Category::where('slug')->first(), потім Product — категорій менше, пошук швидший

**Оптимізація для 1M+ товарів:**
- DB index на `products.slug` + `categories.slug` (обов'язково)
- `routeSlug()` робить max 2 запити (SELECT id FROM... LIMIT 1) — швидко
- Filter-сегменти містять `=` → ніколи не конфліктують з `/{slug}`

---

## PHP: парсинг filter-сегментов

В `CatalogController::show()`:

```php
public function show(Category $category, Request $request, string $filters = ''): View
{
    $initialFilters = $this->parseFilterPath($filters, $category);
    return view('catalog.index', compact('category', 'initialFilters'));
}

private function parseFilterPath(string $filtersPath, Category $category): array
{
    $empty = ['characteristics' => [], 'min_price' => null, 'max_price' => null, 'range_filters' => []];
    $segments = array_filter(explode('/', trim($filtersPath, '/')));
    if (empty($segments)) return $empty;

    // Один запрос для slug→id маппинга характеристик
    $charMap = $category->characteristics()
        ->where('is_active', true)
        ->with(['options:id,characteristic_id,slug'])
        ->get(['characteristics.id', 'characteristics.slug', 'characteristics.is_range_type'])
        ->keyBy('slug');

    $characteristics = [];
    $rangeFilters = [];
    $minPrice = $maxPrice = null;

    foreach ($segments as $segment) {
        if (!str_contains($segment, '=')) continue;
        [$key, $value] = explode('=', $segment, 2);

        if ($key === 'price') {
            [$min, $max] = array_pad(explode('-', $value, 2), 2, null);
            $minPrice = is_numeric($min) ? (int)$min : null;
            $maxPrice = is_numeric($max) ? (int)$max : null;
            continue;
        }

        $char = $charMap[$key] ?? null;
        if (!$char) continue;

        if ($char->is_range_type && str_contains($value, '-')) {
            [$min, $max] = explode('-', $value, 2);
            $rangeFilters[$char->id] = ['min' => (int)$min, 'max' => (int)$max];
            continue;
        }

        $optSlugs = array_filter(explode(',', $value));
        $optMap = $char->options->pluck('id', 'slug')->toArray();
        $optIds = array_values(array_filter(array_map(fn($s) => $optMap[$s] ?? null, $optSlugs)));
        if ($optIds) {
            $characteristics[$char->id] = $optIds;
        }
    }

    return compact('characteristics', 'minPrice', 'maxPrice', 'rangeFilters')
        + ['min_price' => $minPrice, 'max_price' => $maxPrice];
}
```

---

## PHP: построение filter URL

`FilterUrlService` или метод `Facets::buildOptionUrl()`:

```php
// Строит toggle-URL для конкретной опции
// Текущее состояние $filters: [charId => [optId1, optId2]]
// Добавляет или убирает опцию из URL

public function buildOptionUrl(
    array $currentFilters,
    int $charId,
    int $optId,
    string $categorySlug
): string {
    $slugMap = $this->getSlugMap($charId); // charId→slug, optId→slug

    $filtersBySlug = [];
    foreach ($currentFilters['characteristics'] as $cId => $optIds) {
        $cSlug = $slugMap['chars'][$cId] ?? null;
        if (!$cSlug) continue;
        $optSlugs = array_filter(array_map(fn($id) => $slugMap['opts'][$id] ?? null, $optIds));
        if ($optSlugs) $filtersBySlug[$cSlug] = $optSlugs;
    }

    // Toggle опции
    $charSlug = $slugMap['chars'][$charId];
    $optSlug = $slugMap['opts'][$optId];
    $currentOpts = $filtersBySlug[$charSlug] ?? [];

    if (in_array($optSlug, $currentOpts)) {
        $newOpts = array_values(array_filter($currentOpts, fn($s) => $s !== $optSlug));
    } else {
        $newOpts = [...$currentOpts, $optSlug];
    }

    if (empty($newOpts)) {
        unset($filtersBySlug[$charSlug]);
    } else {
        $filtersBySlug[$charSlug] = $newOpts;
    }

    ksort($filtersBySlug);
    $segments = [];
    foreach ($filtersBySlug as $cSlug => $oSlugs) {
        $segments[] = $cSlug . '=' . implode(',', $oSlugs);
    }

    // Добавить price/range в segments если есть
    if ($currentFilters['min_price'] || $currentFilters['max_price']) {
        $segments[] = 'price=' . ($currentFilters['min_price'] ?? '') . '-' . ($currentFilters['max_price'] ?? '');
    }

    $path = '/catalog/' . $categorySlug;
    if ($segments) $path .= '/' . implode('/', $segments) . '/';
    return $path;
}
```

---

## Livewire: Facets.php

```php
// Без #[Url] — URL управляет JS
public array $currentFilters = [
    'characteristics' => [],
    'min_price' => null,
    'max_price' => null,
    'range_filters' => [],
];

public function mount(Category $category, array $initialFilters = []): void
{
    $this->category = $category;
    if (!empty($initialFilters)) {
        $this->currentFilters = $initialFilters;
    }
}

// JS dispatch Livewire.dispatch('filter-changed', {path: '/catalog/phones/color=red/'})
#[On('filter-changed')]
public function onFilterChanged(string $path = ''): void
{
    if ($path) {
        // Получить фильтр-часть пути (всё после /catalog/{category}/)
        $parts = explode('/', ltrim($path, '/'));
        $filterPath = implode('/', array_slice($parts, 2)); // пропустить 'catalog' и slug категории
        $this->currentFilters = app(FilterUrlService::class)
            ->parseFilterPath($filterPath, $this->category);
    }
    $this->dispatch('filtersUpdated', filters: $this->currentFilters);
}
```

---

## Blade: facets.blade.php (опции)

```blade
{{-- Checkbox опция — SEO через <a>, JS через data-url --}}
@php
    $isChecked = in_array($option['id'],
        $this->currentFilters['characteristics'][$facet['characteristic_id']] ?? []);
    $optionUrl = $this->buildOptionUrl($facet['characteristic_id'], $option['id']);
@endphp

<label for="{{ $option['slug'] }}"
       class="flex items-center gap-2 cursor-pointer {{ !$option['count'] ? 'opacity-40' : '' }} {{ $isChecked ? 'font-medium' : '' }}"
       wire:key="option-{{ $option['id'] }}">
    <input type="checkbox"
           id="{{ $option['slug'] }}"
           {{ $isChecked ? 'checked' : '' }}
           {{ !$option['count'] ? 'disabled' : '' }}
           @if($option['count']) data-url="{{ $optionUrl }}" @endif
           class="sr-only">
    {{-- SEO: <a> для поисковиков, JS эмулирует клик по input --}}
    <a href="{{ $optionUrl }}"
       class="js-filter-link flex items-center gap-2 w-full"
       data-input-id="{{ $option['slug'] }}">
        <span class="w-4 h-4 rounded border flex-shrink-0 {{ $isChecked ? 'bg-blue-600 border-blue-600' : 'border-gray-300' }}"></span>
        <span class="text-sm">{{ $option['title'] }}</span>
        <span class="text-xs text-gray-400 ml-auto">({{ $option['count'] }})</span>
    </a>
</label>
```

---

## Blade: facets.blade.php (range слайдер)

```blade
{{-- Range-характеристика (is_range_type = true) --}}
@php
    $rangeState = $this->currentFilters['range_filters'][$facet['characteristic_id']] ?? null;
    $minVal = $rangeState['min'] ?? $facet['range_min'] ?? 0;
    $maxVal = $rangeState['max'] ?? $facet['range_max'] ?? 100;
    $rangeUrl = '/catalog/' . $this->category->slug . '/';  // base (JS достроит)
@endphp
<div class="slider-container js-filter-range-slider"
     data-url="{{ $rangeUrl }}"
     data-param="{{ $facet['characteristic_slug'] }}"
     data-min="{{ $facet['range_min'] ?? 0 }}"
     data-max="{{ $facet['range_max'] ?? 100 }}"
     data-min-value="{{ $minVal }}"
     data-max-value="{{ $maxVal }}">
    <div class="flex gap-2 items-center">
        <input type="number" class="minValue w-20 border rounded px-2 py-1 text-sm" value="{{ $minVal }}">
        <span>—</span>
        <input type="number" class="maxValue w-20 border rounded px-2 py-1 text-sm" value="{{ $maxVal }}">
    </div>
    <div class="range-track relative h-1 bg-gray-200 mt-3 mx-2">
        <div class="range absolute h-full bg-blue-500"></div>
    </div>
    <input type="range" class="minSlider w-full" min="{{ $facet['range_min'] ?? 0 }}" max="{{ $facet['range_max'] ?? 100 }}" value="{{ $minVal }}">
    <input type="range" class="maxSlider w-full" min="{{ $facet['range_min'] ?? 0 }}" max="{{ $facet['range_max'] ?? 100 }}" value="{{ $maxVal }}">
</div>
```

---

## JS: filter.js (адаптация для demo.loc)

```js
// resources/js/catalog/filter.js
// Адаптировано из ServiceMarket. Основные отличия:
// - Используем Livewire 3 (dispatch вместо emit)
// - Передаём путь в событие: Livewire.dispatch('filter-changed', {path})

(() => {
    window.initCatalogFilter = function(component) {
        if (!document.querySelector(component)) return false;
        const selector = component + ' ';

        const parseUrl = (rawUrl) => {
            if (!rawUrl || rawUrl === '#') return null;
            try {
                const url = new URL(rawUrl, window.location.origin);
                return url.pathname + url.search;
            } catch { return null; }
        };

        const dispatchFilterChanged = () => {
            Livewire.dispatch('filter-changed', { path: window.location.pathname });
        };

        const handleClick = (e, attribute) => {
            const filterLink = e.target.closest('.js-filter-link');
            if (filterLink) {
                e.preventDefault();
                e.stopPropagation();
                const inputId = filterLink.getAttribute('data-input-id');
                const input = document.getElementById(inputId);
                if (input && !input.disabled) input.click();
                return;
            }
            const target = e.target.closest(selector + attribute);
            if (target) {
                if (target.tagName === 'A') e.preventDefault();
                const rawUrl = target.getAttribute(target.tagName === 'A' ? 'href' : 'data-url');
                const finalUrl = parseUrl(rawUrl);
                if (!finalUrl) return;
                history.pushState({}, '', finalUrl);
                dispatchFilterChanged();
            }
        };

        // Убираем дублирующие обработчики
        if (window._catalogFilterClickHandler) {
            document.removeEventListener('click', window._catalogFilterClickHandler);
        }
        window._catalogFilterClickHandler = (e) => handleClick(e, '.filters_category a');
        document.addEventListener('click', window._catalogFilterClickHandler);

        if (window._catalogFilterChangeHandler) {
            document.removeEventListener('change', window._catalogFilterChangeHandler);
        }
        window._catalogFilterChangeHandler = (e) => handleClick(e, '.filters_category input');
        document.addEventListener('change', window._catalogFilterChangeHandler);

        // Range слайдеры
        if (document.querySelector('.slider-container.js-filter-range-slider')) {
            if (window._rangeSliderInstance?.destroy) window._rangeSliderInstance.destroy();
            window._rangeSliderInstance = new RangeSlider('.slider-container.js-filter-range-slider');
        }

        // Back/Forward — перезагрузка, чтобы сервер пересчитал состояние
        window.addEventListener('popstate', () => window.location.reload());
    };

    // Запускаем после инициализации Livewire
    document.addEventListener('livewire:init', () => {
        window.initCatalogFilter('.lw-catalog-filter');

        // Переинициализация после Livewire-рендера (filter-product-updated диспатчится из Facets)
        Livewire.on('filter-product-updated', () => {
            window.initCatalogFilter('.lw-catalog-filter');
        });
    });
})();
```

**Важно:** `RangeSlider` класс — из ServiceMarket filter.js (уже готов, копируем без изменений). Не нужен nouislider.

---

## FacetService: range данные

Для range-характеристик нужны `range_min` и `range_max` из TypeSense агрегаций:

```php
// В getCharacteristicsFacets() — добавить для is_range_type:
if ($char->is_range_type) {
    $rangeData = $this->getRangeDataForChar($category, $char);
    return [
        // ... существующие поля
        'is_range_type' => true,
        'range_min' => $rangeData['min'] ?? 0,
        'range_max' => $rangeData['max'] ?? 100,
    ];
}

// Отдельный метод:
private function getRangeDataForChar(Category $category, Characteristic $char): array
{
    return Cache::tags(["category_{$category->id}", "char_range_{$char->id}"])
        ->remember("char_range:{$category->id}:{$char->id}", now()->addHours(1), function () use ($category, $char) {
            // Агрегация из TypeSense или прямой запрос к БД
            $opts = CharacteristicOption::whereHas('products', fn($q) =>
                $q->where('category_id', $category->id)->where('is_active', true)
            )->where('characteristic_id', $char->id)->pluck('value');

            return ['min' => $opts->min() ?? 0, 'max' => $opts->max() ?? 100];
        });
}
```

---

## SEO: canonical и noindex

В `seo.blade.php`:
```php
// filter page = наличие любых path-сегментов с '=' после категории
$pathParts = array_filter(explode('/', request()->path()));
$filterSegments = array_filter(array_slice($pathParts, 2), fn($s) => str_contains($s, '='));
$isFilterPage = !empty($filterSegments);
$noindex = $seoNoindex ?? $isFilterPage;
```

**Canonical:** всегда `/catalog/{category}/` для всех filter-страниц:
```blade
<link rel="canonical" href="{{ url('/catalog/' . request()->segment(2)) }}">
```

---

## Порядок реализации

1. **FilterUrlService** — `buildOptionUrl()`, `parseFilterPath()`, `buildSlugMap()`
2. **Route** — wildcard `/catalog/{category}/{filters?}` с `where('filters', '.*')`
3. **CatalogController** — `parseFilterPath()`, передача `$initialFilters` + `$filterPath` в view
4. **Facets.php** — убрать `#[Url]`, `#[On('filter-changed')]`, `mount(initialFilters)`, убрать `pushUrlState()`
5. **FacetService** — добавить `characteristic_slug`, `range_min`/`range_max` для `is_range_type`
6. **facets.blade.php** — checkbox через `<input data-url>` + `<a class="js-filter-link">`, range slider
7. **filter.js** — адаптация из ServiceMarket (см. выше)
8. **seo.blade.php** — обновить noindex + canonical
9. **Тесты** — фильтр по URL, парсинг path, Facets #[On('filter-changed')]

---

## Anti-triggers (что НЕ делать)

- **НЕ** использовать `wire:click="toggleOption()"` — фильтры через JS + data-url
- **НЕ** использовать `wire:model` для состояния фильтров
- **НЕ** хранить filter state в Livewire snapshot как `#[Url]` — URL управляет JS
- **НЕ** использовать GET-параметры для фильтров (кроме `?page=N`)
- **НЕ** создавать отдельный route для каждой комбинации фильтров
- **НЕ** использовать nouislider — RangeSlider класс из ServiceMarket уже работает
- **НЕ** смешивать product page URL с catalog URL — у товара отдельный prefix `/p/`

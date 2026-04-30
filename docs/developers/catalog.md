# Каталог — Документація для розробника

## Архітектура

```
CatalogController::show()
  └─ view('catalog.index')
       ├─ <livewire:catalog.page>          ← контейнер, передає props вниз
       │    ├─ <livewire:catalog.facets>   ← фільтри (фасети + range sliders)
       │    ├─ <livewire:catalog.sort-bar> ← сортування через URL (?sort=price)
       │    └─ <livewire:catalog.product-list> ← товари + пагінація
       └─ JS: filter.js, filter-range.js, pagination.js, load-more.js
```

---

## Маршрути

```
# Web
GET /catalog/{category:slug}                  → CatalogController@show
GET /catalog/{category:slug}/{filters}/       → теж @show (SEO filter URLs)

# API (для load-more)
GET /api/v1/catalog/{category}/products-html  → Api\v1\CatalogController@getProductsHtml
```

---

## Livewire компоненти

| Компонент | Файл | Відповідальність |
|-----------|------|-----------------|
| `catalog.page` | `Catalog/Page.php` | Контейнер, передає `$category`, `initialFilters`, `initialPage` |
| `catalog.facets` | `Catalog/Facets.php` | Фасети, range слайдери, active filter tags |
| `catalog.sort-bar` | `Catalog/SortBar.php` | Кнопки сортування (`<a href="?sort=...">`) |
| `catalog.product-list` | `Catalog/ProductList.php` | Список товарів + пагінація |

### Підключення сторінки каталогу

```blade
{{-- resources/views/catalog/index.blade.php --}}
<livewire:catalog.page
    :category="$category"
    :initialFilters="$initialFilters"
    :initialPage="$initialPage"
    :basePath="$basePath"
/>
```

`CatalogController` передає у view:
```php
return view('catalog.index', [
    'category'       => $category,
    'basePath'       => parse_url($category->getUrl(), PHP_URL_PATH),
    'initialFilters' => $filters,   // розпарсені з SEO-шляху
    'initialPage'    => $page,
]);
```

---

## SEO Filter URLs

Формат: `/catalog/{category}/{char-slug}={opt1},{opt2}/{price-slug}={min}-{max}/`

| Клас | Відповідальність |
|------|-----------------|
| `FilterUrlService` | Парсинг і побудова SEO filter URLs |
| `FacetService` | Підрахунок фасетів (disjunctive: OR всередині групи, AND між групами) |
| `filter.js` | Перехоплення кліків, `history.pushState`, dispatch `filter-changed` |
| `filter-range.js` | Range slider, debounce 600ms перед Livewire dispatch |

**Подія фільтрації:**
```
JS: filter-changed { path, page }
  → Facets::onFilterChanged()  → dispatch filtersUpdated { filters, page }
    → ProductList::applyFilters()
```

**URL у шаблонах фасетів** — через `data-*` атрибути, JS будує URL сам:
```blade
<input type="checkbox"
    data-char-slug="{{ $facet['characteristic_slug'] }}"
    data-opt-slug="{{ $option['slug'] }}"
    class="js-filter-input">
```

---

## Сервіси

| Сервіс | Призначення |
|--------|-------------|
| `TypeSenseService` | Пошук, фільтрація, сортування через TypeSense |
| `FacetService` | Фасети з Redis-кешем (5 хв, теги `cat_{id}`, `char_{id}`) |
| `FilterUrlService` | Парсинг і побудова SEO-шляхів фільтрів |

---

## Важливо: Alpine + JSON

```blade
{{-- ПРАВИЛЬНО: JSON через data-* атрибут --}}
<div x-data="myComponent($el)"
     data-config="{{ htmlspecialchars(json_encode($cfg), ENT_QUOTES) }}">

{{-- НЕПРАВИЛЬНО: ламає Alpine якщо дані містять лапки --}}
<div x-data="myComponent({ data: @json($data) })">
```

---

## Переклади

```php
__t('...')   // UI тексти фронтенду
__cms('...') // лише адмінка
```

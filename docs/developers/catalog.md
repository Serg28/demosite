# Каталог — Документація для розробника

## Маршрути

```
GET /catalog/{category:slug}     → CatalogController@show
GET /api/v1/catalog/products     → Api\v1\CatalogController@getProducts
GET /api/v1/catalog/{category}/facets  → getFacets
```

## Livewire компоненти

| Компонент | Файл | Відповідальність |
|-----------|------|-----------------|
| `catalog.page` | `Catalog/Page.php` | Контейнер сторінки |
| `catalog.facets` | `Catalog/Facets.php` | Фільтри (facets) |
| `catalog.sort-bar` | `Catalog/SortBar.php` | Сортування |
| `catalog.product-list` | `Catalog/ProductList.php` | Товари + пагінація |

## Пагінація

Два режими паралельно:

**"Показати ще"** (Alpine, без Livewire):
- JS: `resources/js/catalog/product-list.js` → `window.catalogProductList`
- API: `GET /api/v1/catalog/products?page=N&...`
- Дані передаються через `data-config` атрибут (HTML-encoded JSON)

**Нумерована** (Livewire wire:click):
- `ProductList::setPage(int $page)` → `$this->page = N`
- Ключ `wire:key="product-list-{{ $currentPage }}"` — Alpine реініціалізується при зміні сторінки

## Важливо: x-data + JSON

Ніколи не вбудовувати JSON в `x-data=""` напряму:
```blade
{{-- ПРАВИЛЬНО --}}
<div x-data="catalogProductList($el)"
     data-config="{{ htmlspecialchars(json_encode($cfg), ENT_QUOTES) }}">

{{-- НЕПРАВИЛЬНО — ламає Alpine якщо filters містять " --}}
<div x-data="catalogProductList({ filters: @json($filters) })">
```

## Синхронізація Alpine ↔ Livewire

- Livewire диспатчить `product-list-reset` як browser event ПІСЛЯ оновлення DOM
- Alpine слухає його в `init()` і зчитує новий `data-config` з `$el.dataset.config`
- `extraProducts` скидається до `[]`

## Сервіси

- `TypeSenseService` — пошук і фільтрація через TypeSense
- `FacetService` — генерація facets з Redis кешем (15 хв, теги `category_{id}`)
- `OptionSearchService` — нечіткий пошук опцій (Levenshtein)

## Переклади

```php
__t('Показати ще')   // UI тексти
__t('Завантаження...')
```

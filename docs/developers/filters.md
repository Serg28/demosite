# Фільтри каталогу

> Стек: FilterUrlService · FacetService · Livewire Facets · filter.js · filter-range.js

---

## Архітектура URL-фільтрів

Всі активні фільтри зберігаються в PATH (не query-параметрах) для SEO:

```
/catalog/{category}/{segments}/
/catalog/laptops/brand=apple,samsung/price=5000-30000/in_stock=1/
```

**Сегменти:**

| Тип | Формат | Приклад |
|-----|--------|---------|
| Характеристика (чекбокси) | `slug=opt1,opt2` | `brand=apple,samsung` |
| Ціна | `price=min-max` | `price=5000-30000` |
| Характеристика (слайдер) | `slug=min-max` | `diagonal=13-15` |
| Boolean-фільтр | `slug=1` | `in_stock=1` |

---

## Додавання нового boolean-фільтру (toggle)

> Приклад: додаємо фільтр «Новинки» (`is_new=1`).

### Крок 1 — `FilterUrlService`

У константі `BOOLEAN_FILTER_DEFINITIONS` додаємо рядок:

```php
// app/Services/FilterUrlService.php
private const BOOLEAN_FILTER_DEFINITIONS = [
    'in_stock' => 'Тільки в наявності',
    'is_new'   => 'Новинки',     // ← додаємо
];
```

Після цього:
- URL `/catalog/laptops/is_new=1/` автоматично розпарситься
- Toggle у sidebar з'явиться автоматично
- Chip «Новинки ✕» у активних фільтрах з'явиться автоматично

### Крок 2 — Фільтрація продуктів

Додаємо обробку в **двох** місцях:

**`app/Services/FacetService.php`** — в `getDisjunctiveCounts()`:
```php
if ($inStock) { $query->where('p.quantity', '>', 0); }
// → аналогічно для is_new:
if (!empty($filters['is_new'])) { $query->where('p.is_new', true); }
```

**`app/Livewire/Catalog/ProductList.php`** — в методі пошуку TypeSense:
```php
if (!empty($filters['in_stock'])) { $query->where('in_stock', true); }
// → аналогічно:
if (!empty($filters['is_new']))   { $query->where('is_new', true); }
```

### Крок 3 — TypeSense схема (якщо поле нове)

Якщо поле ще не проіндексоване в TypeSense, додати в `config/scout.php` у схему колекції:
```php
['name' => 'is_new', 'type' => 'bool'],
```
Потім переіндексувати: `php artisan scout:import 'App\Models\Product'`.

---

## Додавання характеристики-слайдера (range)

Будь-яка характеристика може відображатись як dual range slider.

### Крок 1 — В БД

Встановити `is_range_type = 1` для характеристики:
```sql
UPDATE characteristics SET is_range_type = 1 WHERE slug = 'diagonal';
```

Або через tinker:
```php
Characteristic::where('slug', 'diagonal')->update(['is_range_type' => true]);
```

### Крок 2 — Дані

**FacetService** автоматично обчислює `range_min`/`range_max` з назв опцій (числових значень). Якщо назви опцій не числові — треба задати значення вручну.

### Крок 3 — UI

`facets.blade.php` автоматично рендерить `js-range-slider` для всіх характеристик з `is_range_type = true`. JavaScript `CatalogRangeSlider` підхоплює будь-який `data-char-slug`.

**URL-сегмент** для range-характеристики: `diagonal=13-15` (slug=min-max).

---

## Як працює dual range slider

Файл: `resources/js/catalog/filter-range.js` (`CatalogRangeSlider`).

**Проблема стекованих range inputs:** обидва inputs мають `absolute inset-0`. Верхній (maxSlider) отримує всі кліки. 

**Рішення:** `mousemove`/`touchstart` на `relative`-контейнері динамічно піднімає `z-index` того бігунка, до якого ближча позиція миші/дотику — до настання кліку. Завдяки цьому клік завжди потрапляє на потрібний input.

**`redraw()`** — виклик після Livewire morph (`commit` hook у `filter.js`). Перечитує `data-current-min`/`data-current-max` з контейнера, відновлює позиції.

---

## FilterUrlService API

```php
// Парсинг path у структуру фільтрів
$filters = $service->parseFilterPath('color=red/in_stock=1', $slugMap);
// → ['characteristics' => [5 => [10]], 'in_stock' => true, 'min_price' => null, ...]

// Toggle boolean фільтру
$url = $service->buildToggleBooleanUrl('/catalog/laptops', 'color=red/', 'in_stock');
// → "/catalog/laptops/color=red/in_stock=1/"

// Toggle чекбоксу характеристики
$url = $service->buildOptionUrl('/catalog/laptops', 'color=red/', 'color', 'blue');
// → "/catalog/laptops/color=red,blue/"

// URL для range
$url = $service->buildRangeUrl('/catalog/laptops', '', 'price', 5000, 30000);
// → "/catalog/laptops/price=5000-30000/"

// Slug map (для категорії)
$slugMap = $service->buildSlugMap($category);

// Список boolean-фільтрів [slug => label]
$defs = FilterUrlService::getBooleanFilterDefinitions();
```

---

## FacetService — структура відповіді

```php
[
    'price' => ['min' => 1000, 'max' => 99000, ...],
    'characteristics' => [
        [
            'characteristic_id'    => 5,
            'characteristic_slug'  => 'brand',
            'characteristic_title' => 'Бренд',
            'is_range_type'        => false,
            'options' => [
                ['id' => 10, 'slug' => 'apple', 'title' => 'Apple', 
                 'count' => 15, 'is_active' => false, 'is_disabled' => false],
            ],
        ],
        [
            'characteristic_id'    => 7,
            'characteristic_slug'  => 'diagonal',
            'characteristic_title' => 'Діагональ',
            'is_range_type'        => true,
            'range_min'            => 11,
            'range_max'            => 17,
            'options'              => [...],
        ],
    ],
    'boolean_filters' => [
        ['slug' => 'in_stock', 'label' => 'Тільки в наявності', 'is_active' => false],
    ],
]
```

`boolean_filters` збагачується `toggle_url` у `Facets::facets()` computed.

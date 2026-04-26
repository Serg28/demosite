# TODO: Уніфікована архітектура пагінації та фільтрації

## Проблема

Зараз пагінація та фільтрація написані конкретно для каталогу (`Catalog\ProductList`).
При появі нових сторінок (Акції, Результати пошуку, Новини) код дублюватиметься.

## Ціль

Підключати пагінацію + фільтрацію до **будь-якої моделі/сторінки** прозоро, мінімальним кодом.

## Архітектура (запропонована)

### 1. Trait `WithSmartPagination` для Livewire компонентів

```php
trait WithSmartPagination
{
    public int $page = 1;

    #[Computed]
    abstract protected function buildQuery(): Builder; // реалізує компонент

    #[Computed]
    public function paginatedResult(): array
    {
        $paginator = $this->buildQuery()->paginate($this->perPage ?? 24);
        return [
            'items'        => $paginator->items(),
            'total'        => $paginator->total(),
            'current_page' => $this->page,
            'last_page'    => $paginator->lastPage(),
            'has_more'     => $this->page < $paginator->lastPage(),
            'per_page'     => $this->perPage ?? 24,
        ];
    }

    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
        $this->dispatch('list-reset');
    }

    protected function resetToFirstPage(): void
    {
        $this->page = 1;
        $this->dispatch('list-reset');
    }
}
```

### 2. Blade компонент `<x-pagination>` (уніфікований)

```blade
{{-- Використання: --}}
<x-pagination :result="$this->paginatedResult" />

{{-- Включає: нумерацію + кнопку Show More (Alpine) --}}
```

### 3. Alpine компонент `listComponent` (загальний)

```js
window.listComponent = function (el) { /* замість catalogProductList */ }
```

Параметризується через `data-config` так само як зараз.

### 4. API ендпоінт (generalized)

```
GET /api/v1/items?model=Product&...filters
GET /api/v1/items?model=Promotion&...filters
```

АБО окремі ендпоінти по ресурсу (чистіше):
```
GET /api/v1/promotions?...
GET /api/v1/search?q=...
```

### 5. Фільтрація — контракт `Filterable`

```php
interface Filterable
{
    public static function applyFilters(Builder $query, array $filters): Builder;
}

// Модель реалізує:
class Product implements Filterable
{
    public static function applyFilters(Builder $query, array $filters): Builder
    {
        // category_id, price_range, characteristics...
    }
}
```

## Сторінки де потрібно

- [x] Каталог (`/catalog/{category}`) — реалізовано вручну
- [ ] Результати пошуку (`/search?q=...`)
- [ ] Акції (`/promotions`) — після Phase 5
- [ ] Новини (`/news`)
- [ ] Сторінка бренду (`/brands/{brand}`)

## Пріоритет

Реалізувати перед другою сторінкою з пагінацією (пошук або акції).
Рефакторити каталог після впровадження trait.

## SEO фільтрів (окрема задача — п. 3.7)

URL-based стан фільтрів:
- `#[Url]` атрибути на `$filters` у Livewire
- Canonical URL для фільтрованих сторінок
- `noindex` для комбінацій що не мають SEO-цінності
- Sitemap для фільтрів з достатньою кількістю товарів

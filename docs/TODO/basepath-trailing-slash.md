# TODO: basePath + trailing slash + DRY pagination

## 1. trailing_slash_enabled — default false, config-driven

Current code hardcodes `rtrim(..., '/')` (no slash). When trailing slash feature
is implemented (ТЗ: `/linecore-demo/ТЗ урлы со слешем в конце.md`):

- Default: **false** (no trailing slash)
- Config: `config('cms.trailing_slash_enabled', false)`

Places that need to respect the setting:
- `app/Services/FilterUrlService.php` — `buildPath()`, `buildClearUrl()`
- `resources/js/catalog/filter.js` — `_buildOptionUrl()`
- `resources/js/catalog/filter-range.js` — `_buildUrl()`

---

## 2. DRY Pagination — trait for all Livewire components

**Problem**: `basePath`, `baseQuery`, `page`, `perPage`, `setPage()`, `buildPaginator()`
are hardcoded in `ProductList`. Should be reusable for any model/page.

**Plan**: create `app/Livewire/Concerns/HasPagination.php` trait:

```php
trait HasPagination
{
    #[Locked]
    public string $paginatorPath = '';

    #[Locked]
    public array $paginatorQuery = [];

    public int $page = 1;
    public int $perPage = 24;

    protected function bootPagination(string $canonicalUrl): void
    {
        $this->paginatorPath  = rtrim((string) parse_url($canonicalUrl, PHP_URL_PATH), '/');
        $this->paginatorQuery = request()->except('page');
    }

    #[On('page-changed')]
    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    protected function makePaginator(array $items, int $total): LengthAwarePaginator
    {
        return (new LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $this->perPage,
            currentPage: $this->page,
        ))
            ->withPath($this->paginatorPath)
            ->appends($this->paginatorQuery);
    }
}
```

**Usage** in any Livewire component:

```php
use HasPagination;

public function mount(Category $category): void
{
    $this->bootPagination($category->getUrl());
}

public function products(): LengthAwarePaginator
{
    $result = ...; // TypeSense/Eloquent
    return $this->makePaginator($result['items'], $result['total']);
}
```

**Standard Blade view** — `resources/views/components/paginator.blade.php`:
- `<x-paginator :paginator="$paginator" />` — works everywhere without configuration

**References to update**:
- `app/Livewire/Catalog/ProductList.php` → use trait, remove duplicate code
- Future: blog, search, news, any paginated list

---

## 3. CatalogList — опціональний merge для highload

**Поточно:** Facets + ProductList = 2 XHR при фільтрації (прийнятно для <500 конкурентних)
**Оптимізація:** Merge в один `CatalogList` = 1 XHR

**Коли робити:** тільки якщо профілювання покаже що 2 XHR є bottleneck.
**Реальний highload** вирішується через Octane + Redis + TypeSense, не кількістю компонентів.

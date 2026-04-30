# TODO: basePath + trailing slash configuration

## Problem

`ProductList::basePath` and `Facets::basePath` are set via:

```php
$this->basePath = rtrim((string) parse_url($category->getUrl(), PHP_URL_PATH), '/');
```

`rtrim(..., '/')` always strips the trailing slash. When the `trailing_slash_enabled`
feature is implemented (see ТЗ: `/linecore-demo/ТЗ урлы со слешем в конце.md`),
the `basePath` must respect the CMS setting.

## Fix needed

After implementing `TrailingSlashUrlGenerator` + `CustomLaravelLocalization`:

```php
$path = (string) parse_url($category->getUrl(), PHP_URL_PATH);
$this->basePath = config('cms.trailing_slash_enabled', true)
    ? rtrim($path, '/')
    : rtrim($path, '/');
// OR: derive from geturl() which will already include trailing slash
$this->basePath = rtrim((string) parse_url(geturl($category->getUrl()), PHP_URL_PATH), '/');
```

The `withPath($this->basePath)` in the paginator generates `?page=N` links —
trailing slash on the base path doesn't affect paginator URL format.

The real concern is `FilterUrlService::buildPath()` and `filter.js::_buildOptionUrl()`
which manually construct paths — those must also respect the trailing slash config.

## References

- `app/Livewire/Catalog/ProductList.php` — `$basePath` prop
- `app/Livewire/Catalog/Facets.php` — `$basePath` prop
- `app/Services/FilterUrlService.php` — `buildPath()`, `buildClearUrl()`
- `resources/js/catalog/filter.js` — `_buildOptionUrl()`
- `resources/js/catalog/filter-range.js` — `_buildUrl()`

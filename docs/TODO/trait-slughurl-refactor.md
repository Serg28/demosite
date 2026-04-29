# TODO: Рефакторинг SlugUrlFieldTrait + Product::getUrl()

## Проблема

`getUrl()` викликається 3 рази на картку товару × 24 = 72 виклики без кешу.
`scopeSlug` містить зайву умову `orWhere(url, '')` — не потрібна в чистій системі з `PrepareModelFields`.

## Що зробити

### 1. Кешування `getUrlOrSlug()` в SlugUrlFieldTrait

Замість `once()` (не враховує екземпляр об'єкта) — кеш у приватній властивості per-instance:

```php
private array $_urlSlugCache = [];

public function getUrlOrSlug(string $locale = ''): string
{
    $locale = $locale ?: App::getLocale();
    return $this->_urlSlugCache[$locale] ??= ($this->getVirtualUrlByLocale($locale) ?? $this->slug ?? '');
}
```

### 2. Кешування `getUrl()` в Product

```php
private array $_urlCache = [];

public function getUrl(string $locale = ''): string
{
    $locale = $locale ?: App::getLocale();
    return $this->_urlCache[$locale] ??= geturl('/product/' . $this->getUrlOrSlug($locale), $locale ?: null);
}
```

### 3. Спростити `scopeSlug` / `scopeSlugIn`

Прибрати `orWhere($urlField, '')` — порожній рядок не зберігається в чистій системі (PrepareModelFields це гарантує):

```php
return $query->where(
    fn (Builder $q) => $q
        ->where($urlField, $slug)
        ->orWhere(fn (Builder $q) => $q->whereNull($urlField)->where($slugField, $slug))
);
```

Це також краще використовує індекс на `{locale}_url`.

## Залежності
- Спочатку портувати `PrepareModelFields` (гарантує NULL замість '')
- Потім — цей рефакторинг

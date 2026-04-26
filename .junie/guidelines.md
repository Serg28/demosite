# demo.loc — Junie/AI Guidelines

## Команди (КРИТИЧНО)

| Команда | Де виконувати |
|---------|---------------|
| `composer`, `php artisan` | ТІЛЬКИ в контейнері `laradock-php-fpm-1` |
| `npm run build/dev` | Локально (поза контейнером) |

```bash
# Войти в контейнер
docker exec -it laradock-php-fpm-1 bash
# pwd → /var/www/demo.loc
```

## Стек

- PHP 8.3, Laravel 12, Livewire 3, AlpineJS (вбудований — не додавати окремо)
- MariaDB, Redis, TypeSense (Scout), Tailwind CSS 3, Vite

## Переклади

```php
__t('Текст')    // фронтенд сторінки — ЗАВЖДИ так
__cms('Текст')  // тільки для адмін-панелі
// Ніколи не використовувати Laravel __()
```

## Alpine + Livewire: передача даних

**Ніколи** не вбудовувати `@json()` з масивами в `x-data=""` атрибут — подвійні лапки ломають HTML.

```blade
{{-- ПРАВИЛЬНО: data-атрибут + $el --}}
<div
    x-data="myComponent($el)"
    data-config="{{ htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8') }}"
>

{{-- НЕПРАВИЛЬНО: JSON безпосередньо в x-data --}}
<div x-data="myComponent({ filters: @json($filters) })">
```

```js
window.myComponent = function (el) {
    const cfg = JSON.parse(el.dataset.config || '{}');
    return { /* ... uses cfg ... */ };
};
```

## Livewire computed properties

```php
#[Computed]  // НЕ потрапляє в wire:snapshot (великі дані)
public function products(): array { ... }

#[Locked]   // захист від JS-маніпуляцій
public array $filters = [];
```

## CMS сумісність

- Всі CMS таблиці в `database/migrations/` (stub з `hasTable` guards)
- `user_id` FK → `unsignedBigInteger` (Laravel bigint users.id)
- `seo_model_h1` — поле ВИДАЛЕНО, не використовувати
- `setting_select` — таблиця ВИДАЛЕНА в CMS v3, не створювати

## Після migrate:fresh

```bash
docker network connect laradock_backend typesense   # якщо відключився
php artisan scout:import 'App\Models\Product'
php artisan cache:clear
```

## API маршрути

`routes/api.php` підключений в `bootstrap/app.php` через `api:` ключ.
Всі API маршрути доступні через `/api/v1/...`.

## Документація для розробників

Готовий функціонал описувати в `docs/developers/`.
TODO і архітектурні рішення — в `docs/TODO/`.

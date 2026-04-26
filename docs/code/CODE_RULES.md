# demo.loc — Правила та конвенції коду

## Середовище та команди

### Docker (ОБОВ'ЯЗКОВО)

| Команда | Де виконувати |
|---------|---------------|
| `php artisan ...` | Всередині контейнера `laradock-php-fpm-1` |
| `composer ...` | Всередині контейнера `laradock-php-fpm-1` |
| `npm ...` | Локально (поза контейнером) |
| Tinker/debug | Всередині контейнера |

```bash
# Увійти в контейнер
docker exec -it laradock-php-fpm-1 bash
# Після входу шлях: /var/www/demo.loc

# Або одноразова команда
docker exec laradock-php-fpm-1 bash -c "cd /var/www/demo.loc && php artisan migrate"
```

### Після migrate:fresh

```bash
# 1. Всі CMS таблиці є в database/migrations/ — запускаються автоматично
# 2. TypeSense мережа
docker network connect laradock_backend typesense
# 3. Реіндексація
php artisan scout:import 'App\Models\Product'
# 4. Кеш
php artisan cache:clear
```

---

## Стек

- **PHP 8.3**, **Laravel 12**, **Livewire 3**, **AlpineJS** (вбудований в Livewire 3)
- **MariaDB 10+**, **Redis**, **TypeSense** (через Laravel Scout)
- **Tailwind CSS 3**, **Vite**
- **CMS**: linecore/linecore-cms пакет

---

## Переклади / Локалізація

```php
// Фронтенд (сторінки сайту)
__t('Додати в кошик')   // НЕ використовуй Laravel __()

// Адмінка CMS
__cms('Зберегти')       // тільки для admin-панелі
```

**Ніколи не змішувати** — `__t()` для сайту, `__cms()` для адмінки.

---

## Livewire 3

```php
// Computed property — НЕ потрапляє в wire:snapshot
#[Computed]
public function products(): LengthAwarePaginator { ... }

// Захист від JS-маніпуляцій
#[Locked]
public array $filters = [];

// Диспатч браузерної події
$this->dispatch('product-list-reset');

// Диспатч до іншого компонента
$this->dispatch('to' => 'catalog-facets', 'filterApplied');
```

- `wire:model` — deferred за замовчуванням (Livewire 3)
- `wire:model.live` — real-time оновлення
- Завжди `wire:key` у loops
- Alpine.js **вже вбудований** — не додавати окремо в package.json

---

## Міграції — CMS сумісність

Усі CMS таблиці є у `database/migrations/` (stub-міграції з `hasTable` guards).
Запускати vendor CMS міграції **НЕ потрібно** — вони дублюють наші stub-и.

### Типи полів (backward compat з linecore-demo)

| Контекст | Правило |
|----------|---------|
| `user_id` FK до `users.id` | `unsignedBigInteger` (Laravel bigint) |
| `role_id` FK до `roles.id` | `unsignedInteger` (CMS increments) |
| Ціни | `decimal(10, 2)` (не int!) |
| JSON поля | `->json()` з `->nullable()` |
| CMS permissions | `text` (серіалізований рядок) |

### Структура таблиці `seo`

```php
$table->id();
$table->integer('seo_id');
$table->string('seo_type');
$table->json('seo_h1')->nullable();
$table->json('seo_title')->nullable();
$table->json('seo_description')->nullable();
$table->json('seo_keywords')->nullable();
$table->json('seo_text')->nullable();
$table->json('seo_canonical')->nullable();
$table->tinyInteger('is_seo_noindex');
$table->tinyInteger('is_seo_nofollow');
$table->unique(['seo_id', 'seo_type']);
```

### Таблиця `settings` (фінальна схема)

```php
$table->increments('id');
$table->string('type');
$table->string('title');
$table->string('slug')->index();
$table->string('value')->nullable();
$table->string('group');
$table->json('value_languages')->nullable();
$table->string('file')->nullable();
$table->tinyInteger('check')->default(0);
$table->longText('textarea')->nullable();
$table->json('textarea_with_languages')->nullable();
$table->json('froala_with_languages')->nullable();
```

**`setting_select` не існує** — видалена в оригіналі (v3 update).

---

## Моделі

- Цінові поля: `decimal` cast, `DECIMAL(10,2)` в БД
- Назви у JSON: `json` cast → `array`
- Завжди eager loading для уникнення N+1
- FK backward-compat з linecore-demo: назви полів і типи **мають збігатися**

---

## Пагінація каталогу

- "Show more" — Alpine.js + `fetch('/api/v1/catalog/products')`, не Livewire state
- Нумерована пагінація — `wire:click="setPage(N)"` без `#[Url]`
- `#[Computed]` на `products()` — не потрапляє в snapshot
- Reset extras при зміні фільтрів: `dispatch('product-list-reset')` (DOM event → Alpine)

---

## Архітектура (SOLID/DRY/KISS)

- **DTO** для передачі даних між шарами
- **Service classes** для бізнес-логіки (не в контролерах)
- **Events/Listeners** для side effects
- **Form Requests** для валідації (не inline в контролері)
- **Enums** для типів (TitleCase ключі: `Monthly`, `Active`)
- Уникай God Class, дублювання, жорстких залежностей

---

## Тестування

- Тести: PHPUnit (не Pest)
- `php artisan test --compact --filter=TestName`
- `RefreshDatabase` — всі CMS stub-міграції запускаються автоматично
- Factories для моделей, custom states перед ручним заповненням
- Кожна зміна — мінімум 1 тест

---

## TypeSense / Scout

- Конфіг: `config/scout.php`
- Імпорт: `php artisan scout:import 'App\Models\Product'`
- FacetService кешує в Redis (15 хв TTL, теги `category_{id}`)
- Atomic locks від cache stampede

---

## Безпека

- Ніколи `env()` поза `config/` файлами
- `route()` замість hardcoded URL
- Завжди валідувати на межі (Form Request / Livewire action)
- Livewire actions — завжди authorize()

---
name: optimize-cache
description: Використовуйте цей навик для аналізу та оптимізації кешування у Laravel/PHP-проєкті (Redis, File, Array cache). Покращує hit rate, TTL, дизайн ключів, інвалідацію, warming та багаторівневе кешування. Ключові тригери: cache, кеш, hit rate, TTL, Redis, optimize cache, оптимізуй кеш, кеш промах, cache miss, cache invalidation, cache warming.
metadata:
  author: linecore
  version: "1.0"
  package: service-market
  updated: "2026-05-07"
---

# Оптимізація кешування

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: є cache miss, низький hit rate, потрібна стратегія кешування, повільні повторювані запити.
- **RU сигнали**: оптимизируй кеш, кеш не работает, низкий hit rate, проблемы с инвалидацией кеша
- **EN signals**: optimize cache, cache miss, low hit rate, cache invalidation issue, cache warming, redis tuning
- **UA сигнали**: оптимізуй кеш, кеш не працює, низький hit rate, проблема з інвалідацією
- **Не використовувати, коли**: потрібне загальне виявлення вузьких місць (detect-bottlenecks) або архітектурна перебудова (refactor-global-architecture).

---

## 1. Зони аналізу

### Cache Hit Rate
- Де використовується `Cache::remember()` / `Cache::get()` — чи є промахи?
- Чи є запити, що виконуються повторно без кешування (Telescope → Query Watcher)
- Занадто короткий TTL → постійні промахи
- Ключі, що перетинаються між запитами різних користувачів (персоналізовані vs shared дані)

### Дизайн ключів
- Ключі мають бути передбачуваними та унікальними: `"products.{locale}.{categoryId}.page{page}"`
- Уникати динамічних частин, що призводять до вибуху кількості ключів (key explosion)
- Не включати в ключ дані, що часто змінюються (timestamp, random)
- Використовувати префікси для namespace: `config('cache.prefix')` + scope

### TTL (Time-to-Live)
- Статичні довідники (категорії, налаштування): довгий TTL (1-24 год), інвалідація по події
- Дані товарів, цін: середній TTL (5-30 хв) або event-driven invalidation
- Персоналізовані дані (кошик, wishlist): короткий TTL або взагалі без кешу
- Результати пошуку: TTL 1-5 хв залежно від частоти оновлення індексу

### Стратегії інвалідації
- **Event-driven** (рекомендовано): `Cache::forget()` / `Cache::tags()->flush()` у Model Observer або після збереження
- **Tag-based**: `Cache::tags(['products', 'category-5'])->flush()` — інвалідувати пов'язані ключі групою
- **TTL-only**: лише для даних, де невелика застарілість прийнятна
- Уникати `Cache::flush()` (очищує весь кеш) у production
- Race condition при інвалідації: використовувати `Cache::lock()` для atomic операцій

### Cache Warming (попередній прогрів)
- Критичні дані (головна, топ-категорії) прогрівати після деплою через Job/Command
- `php artisan cache:warm` — власна команда для прогріву ключових сторінок
- Scheduled warming: `$schedule->command('cache:warm')->hourly()`
- Уникати cold start після `cache:clear` на production — спочатку прогрів, потім switch

### Багаторівневий кеш (Cache Layers)
- **L1**: in-process (static property / `once()`) — для даних у межах одного запиту
- **L2**: Redis (основний кеш) — між запитами
- **L3**: HTTP cache (Varnish / Nginx / CDN) — для публічних сторінок
- `Cache::store('array')` для тестів / ізольованих операцій
- Не дублювати один і той самий ключ на кількох рівнях без стратегії синхронізації

### Памʼять та евікція (Redis)
- Перевірити `maxmemory` та `maxmemory-policy` (рекомендовано `allkeys-lru` для кешу)
- Моніторити `redis-cli info memory` / `used_memory_human`
- Уникати зберігання великих об'єктів (> 1MB) без компресії
- Серіалізація: PHP serialize → json_encode для сумісності та розміру
- Перевірити `CONFIG GET hz` — частота евікції

### Cache Stampede
- При одночасних промахах → всі запити йдуть у БД одночасно
- Рішення: `Cache::lock('key', 10)->block(5, fn() => Cache::remember(...))`
- Або: probabilistic early expiration (оновлювати кеш до закінчення TTL)
- Laravel: `Cache::flexible('key', [30, 60], fn() => ...)` (доступно з Laravel 11)

---

## 2. Процес аналізу

1. **Знайти всі точки кешування**: `grep -r "Cache::" app/` — скласти інвентар
2. **Оцінити hit rate**: Telescope Query Watcher, Redis `MONITOR`, `redis-cli info stats` (keyspace_hits / keyspace_misses)
3. **Перевірити дизайн ключів**: колізії, key explosion, відсутність namespace
4. **Перевірити TTL**: чи відповідає частоті оновлення даних
5. **Перевірити інвалідацію**: де і коли очищується кеш, чи є race condition
6. **Оцінити warming**: чи є cold start проблема, чи потрібен прогрів
7. **Перевірити памʼять Redis**: розмір, евікція, великі ключі (`redis-cli --bigkeys`)

---

## 3. Формат звіту

```
## Аналіз кешування: [файл / endpoint / сервіс]

### Інвентар кешування

| Ключ / патерн         | Driver | TTL  | Інвалідація     | Проблема        |
|-----------------------|--------|------|-----------------|-----------------|
| products.{id}         | Redis  | 60m  | немає           | stale data risk |
| categories.tree       | Redis  | 24h  | Cache::flush()  | занадто широко  |
| search.{query}        | Redis  | 5m   | TTL-only        | key explosion   |

### Знайдені проблеми

| # | Зона          | Опис                              | Severity | Impact       |
|---|---------------|-----------------------------------|----------|--------------|
| 1 | Hit Rate      | search.* — промах 80% запитів     | HIGH     | ~300ms/req   |
| 2 | Інвалідація   | Cache::flush() у ProductObserver  | HIGH     | cold start   |
| 3 | Stampede      | Немає lock у categories.tree      | MEDIUM   | DB spike     |
| 4 | Key Design    | Ключ містить timestamp            | LOW      | key explosion|

### Виправлення

**[#1] Низький hit rate у пошуку**
- Причина: ключ містить сирий query-рядок без нормалізації
- Виправлення: нормалізувати (trim, lowercase, sort params) перед хешуванням
- Приклад: `'search.' . md5(strtolower(trim($query)))`

...

### Рекомендації
- Підключити Redis Insights або `redis-cli --stat` для моніторингу hit rate
- Додати Cache::tags() для групової інвалідації замість flush()
- Розглянути Cache::flexible() для критичних ключів (Laravel 11+)
```

---

## 4. Правила при виправленні

- Дотримуватись `coding-standards-always` при будь-якій зміні коду.
- Не змінювати стратегію кешування глобально без аналізу всіх точок використання.
- Інвалідацію через теги — лише якщо Redis driver підтримує теги (не file/array).
- Кожна зміна TTL або ключа — перевірити всі місця читання/запису цього ключа.
- Cache warming після деплою — через Job у фоні, не синхронно.

---
paths:
  - "**/*.php"
---

# Laravel DI lifecycle та готовність до Octane

## singleton() vs scoped() vs bind()

Неправильний lifecycle — одна з найчастіших помилок, особливо з Octane.

| Метод | Коли використовувати |
|---|---|
| `singleton()` | Сервіс **без стану** (stateless): pipeline, transformer, parser. Один екземпляр на весь процес. |
| `scoped()` | Сервіс **зі станом** (кеш, лічильник, буфер): новий екземпляр на кожен request/queued job. |
| `bind()` | Новий екземпляр при кожному `app()->make()`. |

**Правило:** якщо сервіс має `private array $cache = []` або будь-який накопичуваний стан — тільки `scoped()`.
`singleton()` для stateful сервісу в Octane означає: стан першого request буде видно у всіх наступних.

## Готовність до Laravel Octane

Octane тримає PHP-процес живим між запитами. Будь-який статичний або singleton-стан
накопичується і «протікає» між запитами. Перевіряти при кожній зміні:

**Заборонено в сервісах і моделях:**
- `static` властивості для зберігання стану між викликами (static кеш, static лічильники).
- `singleton()` для сервісів зі станом (використовувати `scoped()`).
- Збереження `Request`, `Auth::user()`, `session()` у властивостях singleton-сервісу —
  вони змінюються між запитами, але сервіс залишається тим самим екземпляром.

**Безпечно:**
- `once()` — Laravel автоматично очищає memoized значення між запитами в Octane.
- `scoped()` сервіси — Octane скидає їх на кожен request.
- Stateless singleton-и (без властивостей, що змінюються після створення).

**Небезпечні патерни — перевіряти при code review:**
```php
// ❌ Статичний кеш протікає між запитами в Octane
class MyService {
    private static array $cache = [];
}

// ❌ singleton зі станом
$this->app->singleton(MyStatefulService::class);

// ✅ scoped для stateful сервісу
$this->app->scoped(MyStatefulService::class);

// ✅ stateless singleton
$this->app->singleton(MyPipeline::class);
```

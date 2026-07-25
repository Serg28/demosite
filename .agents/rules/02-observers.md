---
paths:
  - "**/*.php"
---

# Реєстрація Observer-ів

Observer-и реєструються атрибутом безпосередньо на класі моделі — не в `AppServiceProvider`:

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([MyObserver::class])]
class MyModel extends BaseModel { ... }
```

Кілька observers: `#[ObservedBy([First::class, Second::class])]`.
`AppServiceProvider::boot()` — тільки для observers сторонніх пакетів або коли атрибут недоступний.

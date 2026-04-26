# Архітектурні проблеми linecore-cms

## A-1 — Дві паралельні системи перекладів (дублювання)

**Проблема:**
```
translations_phrases  + translations      → __t()   (фронтенд)
translations_phrases_cms + translations_cms → __cms() (адмінка)
```

Це одна й та сама проблема, вирішена двічі з різними таблицями. Пошукові функції (`__t`, `__cms`) різні, але логіка ідентична: шукаємо фразу → повертаємо переклад.

**Рекомендація:**
Об'єднати в одну систему з контекстом (`scope`):
```
translation_phrases: id, phrase, scope ('frontend'|'admin'|'email')
translations: id, phrase_id, lang, translate
```
Або використати пакет **[spatie/laravel-translation-loader](https://github.com/spatie/laravel-translation-loader)** з DB driver.

---

## A-2 — Кастомна Auth система (застаріла)

**Проблема:** Пакет використовує власну auth систему з таблицями:
- `activations` — замість Laravel `email_verified_at`
- `persistences` — замість Laravel `remember_token`
- `reminders` — замість Laravel `password_reset_tokens`
- `throttle` — замість Laravel Rate Limiter
- `roles` + `role_users` — замість Gates/Policies або Spatie

Ця система базується на старому пакеті **Cartalyst Sentinel** (EOL 2018+).

**Наслідки:**
- Несумісна з Laravel Sanctum, Fortify, Breeze
- Не підтримує MFA/2FA
- `throttle` у БД vs Redis → N-запитів до БД при кожному auth-запиті
- `persistences.code` без salt/hash → вразливість якщо БД скомпрометована

**Рекомендація:**
Замінити на стандартний Laravel Auth:
- `email_verified_at` замість `activations`
- `remember_token` замість `persistences`
- `password_reset_tokens` (Laravel built-in) замість `reminders`
- `Laravel\Sanctum` або `Laravel\Passport` для API auth
- `spatie/laravel-permission` для RBAC
- `cache()->remember()` + Redis замість `throttle` таблиці

---

## A-3 — `settings` таблиця як конфігурація (anti-pattern)

**Проблема:**
```sql
settings: type, title, slug, value, group, value_languages (JSON),
          file, check (tinyint), textarea_with_languages (JSON), froala_with_languages (JSON)
```

- `value_languages` та `textarea_with_languages` та `froala_with_languages` — три окремих JSON для різних UI-типів (textarea vs froala editor). Це логіка представлення в даних.
- `check` — булеве значення з назвою `check`? Незрозуміло що означає.
- `file` — шлях до файлу, але не nullable.
- `type` — string без enum, будь-яке значення.

**Рекомендація:**
```php
// Сучасний підхід — спрощена схема:
settings: id, key (unique), value (json), group, type (enum), timestamps

// Або використати пакет:
// spatie/laravel-settings або glorand/laravel-model-settings
```

---

## A-4 — `revisions` — монолітна audit таблиця

**Проблема:** `revisions` зберігає `old_value` та `new_value` як `text`. При великій кількості ревізій — таблиця росте безконтрольно. Немає TTL, немає cleanup.

**Рекомендація:** Використати **[owen-it/laravel-auditing](https://laravel-auditing.com/)** або **[spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog)** з автоматичним pruning.

---

## A-5 — `tb_tree` — власна реалізація Nested Sets

**Проблема:** Кастомна Nested Sets реалізація без тестів і без пакету.

- `tb_tree`: `lft`, `rgt`, `parent_id`, `depth` — без автоматичного перерахунку
- Немає scope методів (`descendants()`, `ancestors()`, `siblings()`)
- Немає bulk-rebuild індексу

**Рекомендація:** Замінити на **[lazychaser/laravel-nestedset](https://github.com/lazychaser/laravel-nestedset)** або **[staudenmeir/laravel-adjacency-list](https://github.com/staudenmeir/laravel-adjacency-list)**.

---

## A-6 — Міграції у `vendor/` (anti-pattern)

**Проблема:** Міграції лежать у `vendor/linecore/linecore-cms/src/Migrations/` і запускаються окремою командою. Це означає:
- `migrate:status` не показує CMS міграції
- `migrate:fresh` їх не включає
- CI/CD треба знати про окремий шлях
- Тести (`RefreshDatabase`) їх ігнорують

**Стандартний Laravel підхід:**
```php
// В ServiceProvider пакету:
public function boot(): void
{
    $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
}
```

Тоді `php artisan migrate` автоматично підхоплює пакетні міграції, і `migrate:fresh` теж.

**Рекомендація:** Додати `$this->loadMigrationsFrom(...)` у CMS `ServiceProvider`. Всі міграції CMS стануть частиною стандартного потоку.

---

## A-7 — Відсутність ServiceProvider з явним binding

**Проблема:** Не зрозуміло як пакет реєструє свої сервіси без явного перегляду source. Немає інтерфейсів — лише конкретні класи.

**Рекомендація:**
```php
// Контракт:
interface TranslationServiceInterface {
    public function translate(string $phrase, string $lang): string;
}

// Binding у ServiceProvider:
$this->app->bind(TranslationServiceInterface::class, DatabaseTranslationService::class);
```

---

## A-8 — `__t()` і `__cms()` — DB-запит на кожен виклик

**Проблема:** Кожен `__t('Додати в кошик')` генерує SQL-запит до `translations_phrases`. На сторінці каталогу — десятки викликів = десятки запитів.

**Рекомендація:**
1. Кешувати всі переклади для поточної мови в Redis на старті запиту
2. Використати `Cache::rememberForever()` з інвалідацією при зміні перекладів
3. Або перейти на Laravel's вбудований `__()` з DB driver через `spatie/laravel-translation-loader`

```php
// Оптимізована реалізація:
function __t(string $phrase): string {
    static $cache = null;
    if ($cache === null) {
        $lang = app()->getLocale();
        $cache = Cache::remember("translations.{$lang}", 3600, fn() =>
            Translation::with('phrase')->where('lang', $lang)->get()
                ->keyBy(fn($t) => $t->phrase->phrase)
                ->map(fn($t) => $t->translate)
                ->toArray()
        );
    }
    return $cache[$phrase] ?? $phrase;
}
```

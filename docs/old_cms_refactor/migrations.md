# Проблеми у міграціях linecore-cms

## КРИТИЧНО

### M-1 — Іменовані класи міграцій (конфлікт імен)
**Файли:** всі 17 міграцій  
**Проблема:** Всі міграції використовують `class CreateXxxTable extends Migration` (іменований клас).  
У Laravel 9+ це спричиняє `Cannot declare class ... already declared` при паралельному завантаженні.  
**Рішення:** Замінити на анонімні класи `return new class extends Migration { ... };`

```php
// Зараз (проблема):
class CreateTranslationsTable extends Migration { ... }

// Має бути:
return new class extends Migration { ... };
```

---

### M-2 — `Schema::drop()` без `IfExists` у `down()` методах
**Файли:** 12 з 17 міграцій  
**Проблема:** `Schema::drop('users')` — кидає виняток якщо таблиці не існує. При повторному `migrate:rollback` або неповному першому запуску — фатальна помилка.  
**Рішення:** Замінити на `Schema::dropIfExists()` в усіх `down()` методах.

```php
// Зараз:
public function down() {
    Schema::drop('throttle');
}

// Має бути:
public function down(): void {
    Schema::dropIfExists('throttle');
}
```

**Миграції з проблемою:** `create_users_table`, `create_roles_table`, `create_role_users_table`, `create_activations_table`, `create_persistences_table`, `create_reminders_table`, `create_settings_table`, `create_setting_select_table`, `create_tb_tree_table`, `create_throttle_table`, `create_translations_phrases_cms_table`, `create_translations_cms_table`, `create_revisions`

---

### M-3 — Немає `return type declarations` і `void` типів
**Проблема:** Методи `up()` і `down()` без явних return type. PHP 8.3 та PHPStan level 5+ будуть сигналізувати про це.  
**Рішення:** Додати `: void` до всіх `up()` і `down()` методів.

---

## ВАЖЛИВО

### M-4 — Charset `utf8` замість `utf8mb4`
**Файли:** 11 міграцій (усі старіші за 2021)  
**Проблема:** `$table->charset = 'utf8'` — неповна підтримка Unicode (emoji, деякі CJK символи обрізаються). MariaDB 10+ за замовчуванням використовує `utf8mb4`.  
**Рішення:** Прибрати явне `charset/collation` з таблиць (або замінити на `utf8mb4`).

```php
// Зараз:
$table->collation = 'utf8_general_ci';
$table->charset = 'utf8';

// Має бути: (видалити, або замінити на)
$table->collation = 'utf8mb4_unicode_ci';
$table->charset = 'utf8mb4';
```

---

### M-5 — NOT NULL поля без дефолтних значень (runtime помилки)
**Проблема:**
- `activations.completed_at` — `dateTime` NOT NULL, але нова активація ще не completed → вставка порожнього значення або `0000-00-00`.
- `reminders.completed_at` — аналогічно.
- `settings.value`, `settings.file`, `settings.check` — NOT NULL без default.
- `tb_tree.is_active` — `tinyInteger` без default.
- `tb_tree.picture` — `string` NOT NULL, але фото може не бути.

**Рішення:** Додати `->nullable()` або `->default(value)`:
```php
$table->dateTime('completed_at')->nullable();
$table->tinyInteger('is_active')->default(1);
$table->string('picture')->nullable();
$table->text('value')->default('');
```

---

### M-6 — `lang` varchar(2) — занадто короткий
**Таблиці:** `translations`, `translations_cms`  
**Проблема:** `$table->string('lang', 2)` дозволяє лише `ua`, `ru`, `en`. Але локаль Laravel буває `uk_UA` (5 символів) або IETF `uk-UA` (5 символів). При масштабуванні — трекейція без попередження.  
**Рішення:** Змінити на `varchar(10)` — покриває всі реальні локалі.

---

### M-7 — `seo` таблиця: JSON поля без nullable/default
**Файл:** `2021_03_25_000428_create_seo.php`  
**Проблема:** `seo_title`, `seo_description`, `seo_text`, `seo_keywords` — JSON NOT NULL без default. При INSERT без значень — MariaDB кидає error.  
**Рішення:**
```php
$table->json('seo_title')->nullable();
$table->json('seo_description')->nullable();
$table->json('seo_text')->nullable();
$table->json('seo_keywords')->nullable();
$table->tinyInteger('is_seo_noindex')->default(0);
```

---

### M-8 — `revisions.revisionable_id` — signed integer
**Файл:** `2018_02_22_141402_create_revisions.php`  
**Проблема:** `$table->integer('revisionable_id')` — signed, хоча morphs-id завжди unsigned.  
**Рішення:** Використати `$table->morphs('revisionable')` або `unsignedInteger`.

---

### M-9 — `tb_tree.additional_pictures` як `text` замість JSON
**Проблема:** Поле `additional_pictures` зберігається як `text` (або серіалізований рядок), тоді як всі інші картинки у проекті — JSON. Неконсистентно.  
**Рішення:** `$table->json('additional_pictures')->nullable()`

---

## БАЖАНО

### M-10 — `settings` не має `timestamps`
**Проблема:** Неможливо відстежити коли налаштування змінилось.  
**Рішення:** Додати `$table->timestamps()`.

### M-11 — `languages` таблиця: відсутній `name` (назва мови)
**Проблема:** Є лише `language` (код) і `is_active`. Для UI потрібна людська назва: "Українська", "English".  
**Рішення:** `$table->string('name')->nullable()` — назва мови для відображення.

### M-12 — `persistences` без `expires_at`
**Проблема:** "Remember me" токени не мають терміну дії в БД. Застарілі токени накопичуються.  
**Рішення:** `$table->timestamp('expires_at')->nullable()` + periodic cleanup job.

### M-13 — `throttle` — краще зберігати як `rate_limits` через Laravel вбудоване кешування
**Проблема:** Кастомна таблиця `throttle` дублює функціонал Laravel Rate Limiter (Redis-based).  
**Рішення (довгострокове):** Перейти на `cache()->remember()` + Redis замість DB-таблиці.

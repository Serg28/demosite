# Конфлікти схеми CMS з Laravel-додатком

## КРИТИЧНО

### S-1 — Конфлікт таблиці `users` (найбільша проблема)

**CMS міграція** `2018_02_22_093849_create_users_table.php` створює:
```
users: id, email, password, first_name, last_name, image, permissions (text), last_login (datetime)
```

**Laravel-додаток** `0001_01_01_000000_create_users_table.php` створює:
```
users: id, name, email, email_verified_at, password, remember_token, timestamps
```

**Наслідки:**
- При `php artisan migrate:fresh` + `php artisan migrate --path=vendor/.../Migrations` → `Table 'users' already exists` ERROR на CMS міграції
- При запуску CMS першою → `email_verified_at`, `name`, `remember_token` не існують → Laravel Auth падає
- Livewire auth, Laravel Sanctum, Pulse — всі очікують Laravel users schema

**Рішення (варіанти):**

**А) Додати `hasTable` guard у CMS міграцію** (мінімальне втручання):
```php
// В CMS create_users_table::up():
if (Schema::hasTable('users')) {
    // Додати лише відсутні CMS-поля до існуючої Laravel users таблиці
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'first_name')) {
            $table->string('first_name')->after('name')->default('');
        }
        // ...
    });
    return;
}
Schema::create('users', function (Blueprint $table) { ... });
```

**Б) Об'єднати схеми** (правильно, але трудомістко):
Єдина `users` таблиця з усіма полями обох систем:
```
users: id, name, first_name, last_name, email, email_verified_at, password,
       image, permissions, last_login, remember_token, timestamps
```

**В) Поточний workaround у demo.loc:**
CMS-specific поля додаються через окрему міграцію в `database/migrations/`.

---

### S-2 — `translations_phrases` — подвійна реєстрація (stub + vendor)

**Проблема:**  
- `database/migrations/2026_04_26_144720_create_cms_translations_tables.php` — наш stub (для тестів)
- `vendor/.../2016_04_18_132921_create_translations_table.php` — CMS оригінал

При запуску `php artisan migrate --path=vendor/...` ПІСЛЯ нашого stub — CMS міграція пробує створити вже існуючу таблицю. Але CMS має `if (! Schema::hasTable(...))` guards → OK, пропускає.

**Але:** В `migrations` таблиці буде два записи — наш stub і vendor-міграція. При `migrate:rollback` — поведінка непередбачувана.

**Рішення:** Або stub, або vendor — не обидва одночасно. Для prod — запускати vendor. Для tests — потрібен stub.

**Поточний стан demo.loc:** Stub запускається в `database/migrations/`, vendor-міграція НЕ вноситься в `migrations` таблицю (бо `migrate --path` не реєструє в migrations table... насправді ТАК реєструє). Треба перевірити і забезпечити ідемпотентність.

---

### S-3 — Відсутні CMS таблиці при `RefreshDatabase` в тестах

**Таблиці які НЕ створюються при `php artisan test`:**
```
translations_phrases, translations          (потрібні для __t())
translations_phrases_cms, translations_cms  (потрібні для __cms())
tb_tree                                     (потрібна для Tree модель)
settings, setting_select                    (потрібні для site settings)
roles, role_users                           (потрібні для auth/permissions)
seo                                         (потрібна для SEO trait)
languages                                   (потрібна для мультимови)
```

**Поточне рішення (частково):** `database/migrations/2026_04_26_144720_create_cms_translations_tables.php` — покриває лише `translations_phrases` + `translations`.

**Повне рішення:** Або запустити всі CMS міграції в тестах (через `defineDatabaseMigrations()`), або зробити окремий stub для всіх CMS таблиць в `database/migrations/`.

**Рекомендація для пакету:** Надати окремий `CmsTestingServiceProvider` або `TestCase` trait, який автоматично запускає потрібні міграції.

---

### S-4 — `tb_tree` vs `App\Models\Tree` — розбіжність полів

**CMS таблиця** `tb_tree` має:
- `title` JSON, `description` JSON, `slug` string, `template` string, `picture` string, `additional_pictures` text, `is_active` tinyint, `parent_id`, `lft`, `rgt`, `depth`

**Відсутні поля** які часто потрібні:
- `meta_title`, `meta_description` (або через окрему `seo` таблицю — але немає зв'язку з tb_tree!)
- `code` (унікальний slug для програмного доступу)
- `sort_order` (окремо від lft/rgt)

**SEO зв'язок:** Таблиця `seo` має `seo_id` + `seo_type` (morphs), але нема FK constraint → посилальна цілісність не забезпечена на рівні БД.

---

### S-5 — `users.permissions` як `text` (серіалізована строка)

**Проблема:** CMS зберігає права доступу як серіалізований рядок у `users.permissions`. Неможливо зробити SQL запит на права, немає нормалізації.  
**Рішення:** Замінити на `json` cast + перейти на Laravel Policies/Gates або Spatie Laravel-Permission.

---

## Поточний workaround для тестів (demo.loc)

```
database/migrations/2026_04_26_144720_create_cms_translations_tables.php
```

Ця міграція розв'язує проблему S-3 тільки для `__t()`. Решта CMS таблиць поки не потрібні в тестах (немає тестів для Tree, Settings, SEO).

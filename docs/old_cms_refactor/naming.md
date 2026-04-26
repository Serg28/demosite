# Проблеми іменування таблиць і колонок

## N-1 — `id_translations_phrase` замість `translation_phrase_id`

**Таблиця:** `translations`  
**Laravel конвенція:** FK колонка = `{table_singular}_id`  
```sql
-- Зараз (не Laravel-style):
id_translations_phrase INTEGER

-- Має бути:
translations_phrase_id INTEGER UNSIGNED
```

**Вплив:** Query builder, Eloquent `belongsTo` без явного FK name → зламані за замовчуванням.

---

## N-2 — `translations_phrases_cms_id` (надмірно довгий FK)

**Таблиця:** `translations_cms`  
**Проблема:** `translations_phrases_cms_id` — 26 символів. MySQL index key limit може спрацювати.  
```sql
-- Зараз:
translations_phrases_cms_id INTEGER

-- Краще (якщо таблицю перейменувати на cms_phrases):
cms_phrase_id INTEGER UNSIGNED
```

---

## N-3 — `tb_tree` — незрозумілий префікс

**Проблема:** `tb_` — нестандартний префікс (можливо "table"?). Laravel не використовує такі префікси.  
**Рекомендація:** Перейменувати на `pages` або `tree_nodes` — відповідно до призначення.

---

## N-4 — `settings.check` — незрозуміла назва

**Проблема:** Колонка `check` в `settings` — булевий flag без опису. Зарезервоване слово в SQL.  
**Рекомендація:** Перейменувати на `is_visible` або `is_published` залежно від призначення.

---

## N-5 — `setting_select` — незрозуміло що це

**Рекомендація:** Перейменувати на `setting_options` або `settings_choices`.

---

## N-6 — `seo.is_seo_noindex` — надлишковий префікс

**Проблема:** Поле вже у таблиці `seo`, тому `seo_` prefix у назві колонки надлишковий.  
```sql
-- Зараз:
seo_title, seo_description, seo_text, seo_keywords, is_seo_noindex

-- Краще (в контексті таблиці seo):
title, description, text, keywords, is_noindex
```

---

## N-7 — `role_users` замість `user_role` (pivot convention)

**Laravel конвенція:** Pivot таблиця = обидві моделі в алфавітному порядку в однині: `role_user`.  
```sql
-- Зараз: role_users
-- Має бути: role_user (або user_roles залежно від порядку)
```

---

## N-8 — `users.permissions` як text (серіалізований масив)

**Проблема:** Зберігати JSON у `text` без `json` cast = ручна серіалізація/десеріалізація.  
**Рекомендація:** `$table->json('permissions')->nullable()` з Laravel JSON cast.

---

## Зведена таблиця перейменувань

| Зараз | Рекомендовано | Причина |
|-------|---------------|---------|
| `tb_tree` | `pages` або `tree_nodes` | Незрозумілий префікс |
| `role_users` | `role_user` | Laravel pivot конвенція |
| `setting_select` | `setting_options` | Ясніша назва |
| `settings.check` | `settings.is_visible` | SQL reserved word |
| `id_translations_phrase` | `translations_phrase_id` | Laravel FK конвенція |
| `translations_phrases_cms_id` | `cms_phrase_id` | Коротша назва |
| `seo.seo_title` | `seo.title` | Надлишковий префікс |
| `seo.is_seo_noindex` | `seo.is_noindex` | Надлишковий префікс |

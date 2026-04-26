# Рекомендації до рефакторингу linecore-cms

**Дата аналізу:** 2026-04-26  
**Аналізований пакет:** `linecore/linecore-cms`  
**Версія міграцій:** 17 файлів (2016–2021)

---

## Зміст

| Файл | Тема |
|------|------|
| [migrations.md](./migrations.md) | Проблеми у міграціях (17 файлів) |
| [schema_compatibility.md](./schema_compatibility.md) | Конфлікти схеми з Laravel-додатком |
| [architecture.md](./architecture.md) | Архітектурні проблеми пакета |
| [naming.md](./naming.md) | Проблеми іменування таблиць і колонок |

## Пріоритет

| Критично | Важливо | Бажано |
|----------|---------|--------|
| Конфлікт `users` таблиці | utf8 → utf8mb4 | Soft deletes |
| `Schema::drop()` без IfExists | Nullable поля | Timestamps у settings |
| Анонімні класи міграцій | Тип lang varchar(2→10) | Оренування таблиць |

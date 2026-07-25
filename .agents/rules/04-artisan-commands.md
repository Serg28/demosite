---
paths:
  - "**/Console/Commands/*.php"
---

# Правила для Artisan-команд

- Не інжектити «важкі» залежності в `__construct()` команди: API/HTTP-клієнти (Guzzle, `Http::`, зовнішні API-обгортки), сервіси зображень (GD/Imagick), Blade compiler, FilesystemManager, QueueManager, Redis, Elasticsearch.
- Ініціалізувати важкі сервіси в `handle()` через `app()/resolve()`.
- Легкі залежності (репозиторії, дрібні сервіси) можна лишати в конструкторі.
- Обробка великого датасету (потенційно тисячі+ рядків) — `chunk()`/`cursor()`/`lazy()`, не `->get()` цілим масивом.

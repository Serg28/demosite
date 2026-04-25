---
name: cms-buttons
description: Створення кастомних кнопок (Buttons) в адмінці Linecore Builder CMS. Використовуйте, коли потрібна власна кнопка з переходом, AJAX-дією або масовою операцією. Не використовуйте для стандартних actions без окремого класу — для цього є cms-actions. Ключові тригери: custom button, кнопка, ajax action, button class.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Кастомные кнопки в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібна кастомна кнопка з окремим класом.
- **RU сигнали**: создать кастомную кнопку, ajax кнопка, button class
- **EN signals**: create custom button, ajax button, custom admin button class
- **Не використовувати, коли**: не для стандартного набору actions без окремого класу (це cms-actions).

## Коли використовувати
- Додавання кастомной кнопки с своим действием
- Выполнение AJAX запита при клике
- Перенаправление на внешний URL
- Выполнение массовых операций
## Базова структура
**Розташування**: `app/Cms/Buttons/` (может отсутствовать в проекте, створйте при необходимости)
```php
<?php
namespace App\Cms\Buttons;
use Illuminate\Contracts\View\View;
use Linecore\Cms\Interfaces\Button;
use Linecore\Cms\Services\ButtonBase;
class CustomButton extends ButtonBase implements Button
{
  public function show(): View
  {
    $button = [
      "link" => route("custom.action"),
      "ajax" => true,
      "icon" => "star",
      "caption" => "Кастомна дія",
      "id" => "custom-button",
      "massage_start" => "Выполняется...",
      "massage_end" => "Готово!",
    ];
    return view("admin::tb.button", compact("button"));
  }
}
```
## Подключение в Definition
public function buttons()
    return [
        CustomButton::class,
## Приклади кнопок
### Приклад 1: Кнопка с AJAX запитом
class PublishButton extends ButtonBase implements Button
      "link" => "/admin/products/publish",
      "icon" => "publish",
      "caption" => "Опубликовать",
      "id" => "publish-products-btn",
      "massage_start" => "Публикация...",
      "massage_end" => "Опубликовано!",
### Приклад 2: Кнопка перенаправления
class ExportButton extends ButtonBase implements Button
      "link" => route("products.export"),
      "ajax" => false, // Не AJAX, а прямой переход
      "icon" => "download",
      "caption" => "Експорт",
### Приклад 3: Кнопка с подтверждением
class DeleteOldButton extends ButtonBase implements Button
      "link" => route("products.delete-old"),
      "icon" => "trash",
      "caption" => "Видалити старые",
      "id" => "delete-old-btn",
      "massage_start" => "Видалення...",
      "massage_end" => "видалено!",
      "confirm" => true, // Показать подтверждение
      "confirm_message" => "Видалити все записи старше 30 дней?",
### Приклад 4: Кнопка для конкретной записи
class ViewOnSiteButton extends ButtonBase implements Button
    // Получаем ID текущей записи
    $id = request("id");
      "link" => "/products/{$id}",
      "ajax" => false,
      "icon" => "eye",
      "caption" => "На сайте",
      "target" => "_blank", // Открыть в новой вкладке
## Доступные параметры кнопки
| Параметр          | Тип    | Описание                                     |
| ----------------- | ------ | -------------------------------------------- |
| `link`            | string | URL дії                                 |
| `ajax`            | bool   | AJAX запит (true) или переход (false)       |
| `icon`            | string | Иконка (star, pencil, trash, download, etc.) |
| `caption`         | string | Текст кнопки                                 |
| `id`              | string | HTML ID кнопки                               |
| `class`           | string | Дополнительные CSS классы                    |
| `massage_start`   | string | Сообщение при начале выполнения              |
| `massage_end`     | string | Сообщение при завершении                     |
| `confirm`         | bool   | Требовать подтверждение                      |
| `confirm_message` | string | Текст подтверждения                          |
| `target`          | string | Цель ссылки (\_blank, \_self)                |
| `disabled`        | bool   | Отключенная кнопка                           |
## Приклад искористувачания в Definition
namespace App\Cms\Definitions;
use App\Cms\Buttons\PublishButton;
use App\Cms\Buttons\ExportButton;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Services\Actions;
class Products extends Resource
  public $model = Product::class;
  public string $title = "Товары";
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->delete();
  public function buttons()
    return [PublishButton::class, ExportButton::class];
## Важливі нюанси
1. **Namespace**: Кнопки располагаются в `App\Cms\Buttons\`
2. **Interface**: Реализуйте интерфейс `Linecore\Cms\Interfaces\Button`
3. **View**: Возвращайте view через `admin::tb.button`
4. **AJAX**: Для AJAX укажите `ajax => true` и настройте роут
5. **Иконки**: Використовуйте доступні иконки из библиотеки
## Перегляд существующих кнопок
```bash
ls -la app/Cms/Buttons/
## Створення роута для кнопки
// routes/admin.php
Route::post("/products/publish", [ProductController::class, "publish"])->name(
  "products.publish"
);
// ProductController.php
public function publish()
    Product::where('is_active', false)->update(['is_active' => true]);
    return response()->json(['success' => true]);

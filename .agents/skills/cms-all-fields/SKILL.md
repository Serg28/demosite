---
name: cms-all-fields
description: Довідник усіх доступних полів (Fields) у Linecore Builder CMS. Використовуйте, коли потрібно підібрати тип поля, параметри та приклади використання. Не використовуйте як інструкцію для створення нового розділу. Ключові тригери: fields, тип поля, справочник полей, options.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Все поля (Fields) в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли треба швидко підібрати тип поля та параметри.
- **RU сигнали**: какое поле использовать, справочник полей, типы полей cms
- **EN signals**: fields reference, choose field type, cms field options
- **Не використовувати, коли**: не як покроковий гайд по створенню нового розділу.

## Розташування
- **Встроенные поля**: `vendor/vendor/linecore/linecore-cms/src/Http/Fields/`
- **Кастомные поля**: `app/Cms/Fields/`
## Перелік всех полей
### Базовые поля
| Поле     | Класс      | Описание            |
| -------- | ---------- | ------------------- |
| Text     | `Text`     | Текстовое поле      |
| Textarea | `Textarea` | Многострочный текст |
| Number   | `Number`   | Числовое поле       |
| Checkbox | `Checkbox` | Чекбокс (да/нет)    |
| Select   | `Select`   | Выпадающий перелік   |
| Hidden   | `Hidden`   | Скрытое поле        |
### Поля для контента
| Поле       | Класс        | Описание               |
| ---------- | ------------ | ---------------------- |
| Image      | `Image`      | Загрузка зображення   |
| MultiImage | `MultiImage` | Несколько зображений  |
| File       | `File`       | Загрузка файла         |
| MultiFile  | `MultiFile`  | Несколько файлів       |
| Tinymce    | `Tinymce`    | WYSIWYG редактор       |
| Froala     | `Froala`     | Альтернативный WYSIWYG |
### Поля для пов’язей
| Поле                  | Класс                   | Описание             |
| --------------------- | ----------------------- | -------------------- |
| Foreign               | `Foreign`               | Связь belongsTo      |
| ForeignAjax           | `ForeignAjax`           | Связь с AJAX поиском |
| ManyToMany            | `ManyToMany`            | Связь ManyToMany     |
| ManyToManyAjax        | `ManyToManyAjax`        | ManyToMany с AJAX    |
| ManyToManyMultiSelect | `ManyToManyMultiSelect` | Multiple Select      |
### Специальные поля
| Поле        | Класс         | Описание      |
| ----------- | ------------- | ------------- |
| Id          | `Id`          | ID записи     |
| Date        | `Date`        | Дата          |
| Datetime    | `Datetime`    | Дата и время  |
| Color       | `Color`       | Выбор цвета   |
| Password    | `Password`    | Поле пароля   |
| Email       | `Email`       | Email поле    |
| Json        | `Json`        | JSON дані   |
| Permissions | `Permissions` | Права доступа |
### Служебные поля
| Поле          | Класс           | Описание             |
| ------------- | --------------- | -------------------- |
| Readonly      | `Readonly`      | Лише читання        |
| ReadonlyField | `ReadonlyField` | Кастомное readonly   |
| Virtual       | `Virtual`       | Виртуальное поле     |
| Definition    | `Definition`    | Вложенный Definition |
| Custom        | `Custom`        | Кастомное поле       |
## Детальное опис полей
### Text
```php
Text::make("Назва", "title")
  ->language() // Мультиязычное
  ->filter() // Фільтрування
  ->sortable() // Сортування
  ->rules(["required"]) // Валидация
  ->className("col-md-6") // CSS класс
  ->comment("Підказка") // Комментарий
  ->transliteration("slug", true) // Транслитерация
  ->onlyForm() // Только форма
  ->onlyList(); // Только перелік
```
### Textarea
Textarea::make("Описание", "description")
  ->language()
  ->rows(5) // Количество строк
  ->cols(50); // Количество колонок
### Number
Number::make("Цена", "price")
  ->filter()
  ->sortable()
  ->rules(["required", "numeric"])
  ->min(0)
  ->max(1000000);
### Checkbox
Checkbox::make("Активно", "is_active")
  ->fastEdit() // Быстрое редагування
  ->className("col-md-6")
  ->default(true); // Значение по умолчанию
### Select
Select::make("Статус", "status")
  ->options([
    "draft" => "Черновик",
    "published" => "Опубликовано",
    "archived" => "В архиве",
  ])
  ->default("draft");
### Image
Image::make("Фото", "picture")
  ->rules(["image", "max:2048"])
  ->resize(800, 600) // Зміна размера
  ->watermark() // Водяной знак
  ->path("products/"); // Путь для збереження
### MultiImage
MultiImage::make("Галерея", "gallery")
  ->max(10) // Макс. количество
  ->rules(["image"]);
### Foreign (звʼязок с другой моделью)
Foreign::make("Категория", "category_id")
  ->options((new Options("category"))->isJson())
  ->placeholder("Выберите категорию");
### ForeignAjax (AJAX пошук)
ForeignAjax::make("Бренд", "brand_id")
  ->options((new Options("brand"))->isJson())
  ->setMinimumSearchLength(2) // Мин. символов для поиска
  ->filter();
### ManyToMany
ManyToMany::make("Теги")
  ->options((new Options("tags"))->isJson())
  ->keyField("name"); // Поле для отображения
### ManyToManyAjax
ManyToManyAjax::make("Категории")->options(
  (new Options("categories"))->isJson()
);
### Date
Date::make("Дата публикации", "publish_at")
  ->format("DD.MM.YYYY");
### Datetime
Datetime::make("Дата и время", "created_at")->format("DD.MM.YYYY HH:mm");
### Color
Color::make("Цвет", "color")->default("#ffffff");
### Password
Password::make("Пароль", "password")
  ->generate(true) // Генерировать пароль
  ->length(12); // Длина
### Json
Json::make("Доп. дані", "extra_data")->mode("edit"); // Режим редагування
### ReadonlyField
ReadonlyField::make("створн", "created_at")->handleUsing(function ($row) {
  return $row->created_at->format("d.m.Y H:i");
});
### Id
Id::make("#", "id")
### Hidden
Hidden::make("ID користувача", "user_id")->default(auth()->id());
### Virtual
Virtual::make("Полное имя")->handleUsing(function ($row) {
  return $row->first_name . " " . $row->last_name;
## Options (для пов’язаних полей)
use Linecore\Cms\Fields\Relations\Options;
// Базове використання
->options((new Options('model'))->isJson())
// С сортувкой
->options((new Options('model'))->orderBy('title', 'asc'))
// С доп. условием
->options((new Options('model'))->where('is_active', true))
// С выбором ключевого поля
->options((new Options('model'))->keyField('name'))
// Без JSON
->options((new Options('model')))
## Параметры полей
### Визуальные
| Метод           | Описание              |
| --------------- | --------------------- |
| `title()`       | Заголовок поля        |
| `className()`   | CSS классы            |
| `comment()`     | Комментарий/підказка |
| `placeholder()` | Плейсхолдер           |
| `default()`     | Значение по умолчанию |
### Функциональные
| Метод        | Описание               |
| ------------ | ---------------------- |
| `filter()`   | Включить фільтрувию    |
| `sortable()` | Включить сортування    |
| `fastEdit()` | Быстрое редагування |
| `readonly()` | Лише читання          |
| `disabled()` | Отключено              |
| `required()` | Обязательное           |
### Ограничение видимости
| Метод        | Описание        |
| ------------ | --------------- |
| `onlyForm()` | Только в форме  |
| `onlyList()` | Только в переліку |
### Валидация
| Метод      | Описание            |
| ---------- | ------------------- |
| `rules()`  | Правила валидации   |
| `unique()` | Уникальное значение |
## Приклад кастомного Options
Options::make("brands")
  ->isJson()
  ->where("is_active", true)
  ->orderBy("title", "asc")
  ->keyField("name");
## Кастомные поля в проекте
В проекте уже есть множество готовых кастомних полей в `app/Cms/Fields/`:
### Поля для роботи с заказами
- `OrderProducts.php` - товары в заказе
- `OrderDelivery.php` - доставка заказа
- `OrderContacts.php` - контакты заказа
- `OrderNum.php` - номер заказа
- `OrderID.php` - ID заказа
- `OrderStatuses.php` - статусы заказа
### Поля для товаров
- `ProductCharacteristics.php` - характеристики товара
- `ProductsCodes.php` - коды товаров
- `ProductsAnalogsExternalIds.php` - аналоги товаров
### Поля для категорий
- `CategoryCharacteristic.php` - характеристики категории
- `CategoryPriceRanges.php` - диапазоны цен
### AJAX поля
- `ForeignAjaxCategories.php` - вибір категории
- `ForeignAjaxProduct.php` - вибір товара
- `ForeignAjaxBrand.php` - вибір бренда
- `ForeignAjaxUser.php` - вибір користувача
- `ForeignAjaxManager.php` - вибір менеджера
- `ForeignAjaxCity.php` - вибір города
- `ForeignAjaxNPWarehouses.php` - вибір отделения НП
- `ManyToManyAjaxCategories.php` - множественный вибір категорий
- `ManyToManyAjaxProducts.php` - множественный вибір товаров
- `Price.php` - поле цены с конвертацией
- `TextExt.php` - расширенное текстовое поле
- `Tinymce.php` - WYSIWYG редактор
- `ForeignTreeCategory.php` - дерево категорий
## Перегляд всех кастомних полей
```bash
ls -la app/Cms/Fields/
## Важливі нюанси
1. **language()**: Работает с Text, Textarea, Tinymce, Image
2. **filter()**: Добавляет поле в фільтри переліку
3. **sortable()**: Позволяет сортувать по полю
4. **fastEdit()**: Только для Checkbox
5. **isJson()**: Обязателен для Foreign/ForeignAjax с JSON даними

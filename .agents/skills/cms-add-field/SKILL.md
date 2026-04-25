---
name: cms-add-field
description: Додавання нового поля в існуючий розділ адмінки Linecore Builder CMS. Використовуйте цей навик для додавання текстових, медіа, булевих полів та звʼязків у Definition. Не використовуйте для створення нового розділу з нуля — для цього є cms-resource-definition. Ключові тригери: field, поле, додати поле, add field.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Додавання поля в существующий Definition

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібно додати поле в існуючий Definition.
- **RU сигнали**: добавить поле, добавить колонку в админку, field в форму
- **EN signals**: add field, add form field, add admin field
- **Не використовувати, коли**: не для створення нового Definition з нуля (це cms-resource-definition).

## Коли використовувати
- Користувач просить додати новое поле в адмінку
- Потрібно расширить существующий розділ новым атрибутом
- Потрібно додати звʼязок с другой моделью
## Алгоритм дій
### 1. Найдите Definition
Определите, в каком Definition потрібно додати поле:
- **Categories**: `app/Cms/Definitions/Categories.php`
- **Products**: `app/Cms/Definitions/Products.php`
- Другие: `app/Cms/Definitions/{Name}.php`
### 2. Определите группу полей
Выберите существующую группу или створйте новую:
- 'Основное'
- 'Зображення'
- 'SEO'
- 'Настройки'
- и т.д.
### 3. Выберите тип поля
**Text (текстовое поле)**
```php
Text::make("Назва поля", "field_name")
  ->language() // мультиязычное
  ->filter() // фільтрація
  ->sortable() // сортування
  ->rules(["required"]) // валидация
  ->className("col-md-6") // CSS класс
  ->comment("Підказка"); // комментарий
```
**Checkbox (галочка)**
Checkbox::make("Активно", "is_active")
  ->filter()
  ->sortable()
  ->fastEdit() // быстрое редагування
  ->className("col-md-6");
**Image (зображення)**
Image::make("Фото", "picture")->rules(["image", "max:2048"]);
**Foreign (звʼязок с другой моделью)**
Foreign::make("Категория", "category_id")
  ->options((new Options("category"))->isJson())
  ->sortable();
**ForeignAjax (вибір з пошуком)**
ForeignAjax::make("Бренд", "brand_id")->options(
  (new Options("brand"))->isJson()
);
**Number (число)**
Number::make("Цена", "price")
**Select (вибір из переліку)**
Select::make("Статус", "status")
  ->options([
    "draft" => "Черновик",
    "published" => "Опубликовано",
  ])
  ->filter();
**Textarea (многострочный текст)**
Textarea::make("Описание", "description")->language();
### 4. Добавьте поле в Definition
public function fields(): array
{
    return [
        'Основное' => [
            // Существующие поля...
            // Новое поле
            Text::make('Новое поле', 'new_field')
                ->className('col-md-6')
                ->comment('Описание поля'),
        ],
    ];
}
## Приклад: Додавання поля "Приоритет" в категории
### Исходный код (фрагмент)
// app/Cms/Definitions/Categories.php
public function fields()
            Id::make('#', 'id')->sortable(),
            Text::make('Назва', 'title')->language()->filter()->sortable(),
            // ...
### После додавання
            Number::make('Приоритет', 'priority')
                ->sortable()
                ->comment('Чем меньше число, тем выше в переліку'),
## Приклад: Додавання звʼязки с брендом
use Linecore\Cms\Fields\ForeignAjax;
use Linecore\Cms\Fields\Relations\Options;
// У методі fields():
'Связи' => [
    ForeignAjax::make('Бренд', 'brand_id')
        ->options((new Options('brand'))->isJson())
        ->comment('Выберите бренд'),
],
## Приклад: Додавання галочки
'Настройки' => [
    Checkbox::make('Показывать на главной', 'show_on_homepage')
        ->className('col-md-6')
        ->comment('Выводить в блоке рекомендуемых'),
## Важливі нюанси
1. **Миграция**: Если поле новое в БД, створйте миграцию
2. **Модель**: Добавьте поле в `$fillable` модели, если потрібно
3. **Валидация**: Добавьте правила в `rules()` при необходимости
4. **Порядок**: Поля отображаются в том порядке, в котором добавлены
5. **Группы**: Використовуйте логичную группировку
## Проверка після додавання
```bash
# Проверьте синтаксис
php -l app/Cms/Definitions/Categories.php
# Очистите кеш
php artisan cache:clear
## Типичные ошибки
1. **Поле не сохраняется**: Добавьте в `$fillable` модели
2. **Ошибка валидации**: Проверьте правила
3. **Поле не отображается**: Проверьте назва файла Definition

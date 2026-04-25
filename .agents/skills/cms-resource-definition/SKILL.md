---
name: cms-resource-definition
description: Створення нового розділу (Definition) в адмінці Linecore Builder CMS. Використовуйте для повного CRUD-розділу: модель, поля, actions, фільтри, форма редагування. Не використовуйте для Tree-сторінок — для цього є cms-tree-page. Ключові тригери: definition, resource, створити розділ, CRUD адмінки.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Створення Definition в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібно створити новий CRUD-розділ адмінки (Definition).
- **RU сигнали**: создать definition, новий раздел адмінки, CRUD раздел
- **EN signals**: create definition, admin resource, crud resource
- **Не використовувати, коли**: не для Tree-структури сторінок (це cms-tree-page).


## Коли використовувати

- Користувач просить створити новий розділ в адмінці
- Потрібно додати CRUD для нової моделі
- Потрібно налаштувати поля, фільтри, сортування
- Створення форми редагування/створення записів

## Базова структура Definition

```php
<?php
namespace App\Cms\Definitions;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\{Text, Image, Checkbox, Select, Id};
use Linecore\Cms\Services\Actions;
class ExampleResource extends Resource
{
  public $model = ExampleModel::class;
  public string $title = "Назва ресурса";
  protected $orderBy = "id desc";
  protected $perPage = [20, 100, 1000];
  public function fields(): array
  {
    return [
      "Группа полей 1" => [
        // поля
      ],
      "Группа полей 2" => [
    ];
  }
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->clone()
      ->revisions()
      ->delete();
}
```

## Розташування файлів

- **Definition**: `app/Cms/Definitions/`
- **Кастомные поля**: `app/Cms/Fields/`
- **Кастомные сервисы**: `app/Cms/Services/`
- **Кастомные кнопки**: `app/Cms/Buttons/` (может отсутствовать в проекте, створйте при необходимости)
- **Шаблоны полей**: `resources/views/cms/fields/`
- **Шаблоны форм**: `resources/views/cms/tb/`
- **Шаблоны переліку**: `resources/views/cms/templates/`

## Типи полів

Дивись скіли `cms-all-fields` та `cms-custom-field` 
Для полів потрібно додавати валідацію через `rules()`
При неоднозначності, яке поле і валідацію використати, запитуй у користувача.

### Text

Text::make("Назва", "title")
->language() // Мультиязычное поле
->filter() // Фільтрування в переліку
->sortable() // Сортування в переліку
->rules(["required", "max:255"]) // Валидация
->className("col-md-6") // CSS классы
->comment("Підказка") // Комментарий
->transliteration("slug", true); // Автотранслитерация в поле slug

### Id

Id::make("#", "id")
->sortable()
->filter();

### Checkbox

Checkbox::make("Активно", "is_active")
->filter()
->fastEdit() // Быстрое редагування в переліку
->className("col-md-6");

### Image

Image::make("Фото", "picture")
->rules(["image", "max:2048"])
->resize(800, 600)
->watermark();

### Select

Select::make("Статус", "status")
->options([
"draft" => "Черновик",
"published" => "Опубликовано",
"archived" => "В архиве",
])
->sortable();

### Foreign (пов’язана модель)

Foreign::make("Категория", "category_id")
->options((new Options("category"))->isJson())

### ForeignAjax (AJAX вибір з пошуком)

ForeignAjax::make("Менеджер", "manager_id")
->options((new Options("manager"))->isJson())

### Number

Number::make("Цена", "price")
->rules(["required", "numeric"]);

### Textarea

Textarea::make("Описание", "description")
->language()
->rows(5);

### Hidden

Hidden::make("ID користувача", "user_id");

### ReadonlyField (лише читання)

ReadonlyField::make("Дата створення", "created_at")->handleUsing(function (
$row
) {
return $row->created_at->format("d.m.Y");
});

### ManyToMany (багато-до-багатьох)

ManyToMany::make("Теги")->options((new Options("tags"))->isJson());

### ManyToManyMultiSelect

ManyToManyMultiSelect::make("Категории")->options(new Options("categories"));

## Групування полів

public function fields(): array
'Основное' => [
Id::make('#', 'id')->sortable(),
Text::make('Назва', 'title')->language()->filter()->sortable(),
],
'Зображення' => [
Image::make('Фото', 'picture'),
Image::make('Галерея', 'gallery'),
'SEO' => [
Text::make('Meta Title', 'meta_title')->language(),
Text::make('Meta Description', 'meta_description')->language(),
'Настройки' => [
Checkbox::make('Активно', 'is_active')->filter(),

## Налаштування дій (actions)

public function actions()
->insert() // Додати
->update() // Редагувати
->clone() // Клонувати
->revisions() // Історія змін
->delete() // Видалити
// ->export() // Експорт
// ->import() // Імпорт

## Підключення SEO-полів

use App\Models\MorphOne\Seo;
// У методі fields():
'SEO' => Seo::fieldsForDefinitions($this->model),

## Кастомна логіка

### Перевизначення метода збереження

public function saveAddForm($request): array
    $result = parent::saveAddForm($request);
// Кастомна логіка після збереження
$record = $this->model()->find($result['id']);
$record->update(['slug' => \Str::slug($request->title)]);
return $result;

### Кастомний запит для переліку

public function getQuery()
return parent::getQuery()->where('is_active', true);

## Приклади

### Приклад 1: Простий ресурс

class Products extends Resource
public $model = Product::class;
public string $title = "Товары";
"Основное" => [
Id::make("#", "id")
->sortable()
->filter(),
Text::make("Назва", "title")
->language()
->filter()
->sortable(),
Text::make("Код", "code")
Checkbox::make("Активен", "is_active")
->fastEdit(),
"Зображення" => [Image::make("Фото", "picture")],

### Приклад 2: Ресурс с повʼязными полями

use Linecore\Cms\Fields\ForeignAjax;
use Linecore\Cms\Fields\Relations\Options;
class Orders extends Resource
public $model = Order::class;
public string $title = "Заказы";
"Заказ" => [
Id::make("#", "id")->sortable(),
ForeignAjax::make("Клиент", "user_id")->options(
(new Options("user"))->isJson()
),
ForeignAjax::make("Статус", "status_id")->options(
(new Options("orderStatus"))->isJson()

## Важливі нюанси

1. **Наслідування**: Використовуйте `extends Resource` для обычных ресурсов
2. **Модель**: Вкажіть полный путь к модели в `$model`
3. **Назва**: Використовуйте транслит в названии класса (Products, NotArticles, etc.)
4. **URL**: После створення Definition доступ по URL `/admin/{назва}-в-множині`
5. **Кеширование**: При необходимости очистки кеша используйте `$cacheTag`

## Команди для роботи

```bash
# Очищення кешу визначень
php artisan cache:clear
# Перегляд доступних определений
php artisan tinker --execute="echo implode(', ', array_keys((new \Linecore\Cms\Tree)->definitions()));"
```

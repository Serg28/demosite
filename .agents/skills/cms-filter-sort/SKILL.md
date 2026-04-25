---
name: cms-filter-sort
description: Налаштування фільтрації та сортування в адмінці Linecore Builder CMS. Використовуйте для filter/sort полів у перелікух, порядку за замовчуванням і кастомних умов. Не використовуйте для фронтенд-фільтрів каталогу. Ключові тригери: filter, sort, фільтрація, сортування.
metadata:
  version: "1.0"
  author: linecore
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Фільтрування и сортування в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли треба налаштувати filter/sort у переліку адмінки.
- **RU сигнали**: фільтрування в админке, сортування переліку, filter sort
- **EN signals**: admin list filtering, default sorting, filter sort config
- **Не використовувати, коли**: не для фронтенд-фільтрів каталогу.

## Коли використовувати
- Налаштування фільтрів в переліку записів
- Налаштування сортувки по умолчанию
- Створення кастомних фільтрів
- Налаштування полей для поиска
## Базова налаштування
### Включение фільтрувии
```php
Text::make("Назва", "title")->filter(); // Включить фільтр
```
### Включение сортувки
Text::make("Назва", "title")->sortable(); // Включить сортування
### Оба вместе
Text::make("Назва", "title")
  ->filter()
  ->sortable();
## Типы полей с фільтрувией
### Text
  ->sortable()
  ->filterScope("title"); // Кастомний scope
### Checkbox (булево поле)
Checkbox::make("Активно", "is_active")
  ->fastEdit(); // Быстрое редагування
### Select
Select::make("Статус", "status")
  ->options([
    "draft" => "Черновик",
    "published" => "Опубликовано",
  ])
  ->filter() // фільтр
  ->sortable(); // Сортування
### Foreign (повʼязное поле)
Foreign::make("Категория", "category_id")
  ->options((new Options("category"))->isJson())
  ->filter() // фільтр по ID
### ForeignAjax
ForeignAjax::make("Бренд", "brand_id")
  ->options((new Options("brand"))->isJson())
  ->filter();
### Number
Number::make("Цена", "price")
### Id
Id::make("#", "id")
## Сортування по умолчанию
### В Definition
protected $orderBy = 'id desc';  // По ID убывание
protected $orderBy = 'title asc';  // По названию возрастание
protected $orderBy = 'created_at desc';  // По дате створення
### Несколько полей
protected $orderBy = 'is_active desc, title asc';
## Кастомные фільтри (filterScope)
### Простий scope
Text::make('Email', 'email')
    ->filter()
    ->filterScope('emailFilter');
// В модели
public function scopeEmailFilter($query, $value)
{
    return $query->where('email', 'like', "%{$value}%");
}
### Сложный scope
Text::make('Назва', 'title')
    ->filterScope('titleContains');
public function scopeTitleContains($query, $value)
    return $query->where('title_uk', 'like', "%{$value}%")
        ->orWhere('title_ru', 'like', "%{$value}%");
### Scope с выбором
Select::make('Статус', 'status')
    ->options([
        'draft' => 'Черновик',
        'published' => 'Опубликовано',
    ])
    ->filterScope('statusFilter');
public function scopeStatusFilter($query, $value)
    if ($value === 'all' || !$value) {
        return $query;
    }
    return $query->where('status', $value);
## Пагинация
### Налаштування количества на странице
protected $perPage = [20, 50, 100, 200];
// Или
protected $perPage = 20;  // Только одно значение
### В ресурсе
class Products extends Resource
  protected $perPage = [20, 100, 1000];
  // ...
## Приклади Definition с фільтрами
<?php
namespace App\Cms\Definitions;
use App\Models\Product;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\{Id, Text, Checkbox, ForeignAjax, Number};
use Linecore\Cms\Fields\Relations\Options;
use Linecore\Cms\Services\Actions;
  public $model = Product::class;
  public string $title = "Товары";
  // Сортування по умолчанию
  protected $orderBy = "id desc";
  // Количество на странице
  public function fields(): array
  {
    return [
      "Основное" => [
        Id::make("#", "id")
          ->filter()
          ->sortable(),
        Text::make("Назва", "title")
          ->language()
        Text::make("Код", "code")
        ForeignAjax::make("Категория", "category_id")
          ->options((new Options("category"))->isJson())
        Checkbox::make("Активен", "is_active")
          ->sortable()
          ->fastEdit(),
        Number::make("Цена", "price")
      ],
    ];
  }
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->delete();
## Важливі нюанси
1. **filter()**: Добавляет поле в строку фільтрів
2. **sortable()**: Добавляет возможность сортувки кликом по заголовку
3. **filterScope()**: Позволяет кастомизировать логику фільтрувии
4. **fastEdit()**: Позволяет редагувати значение кликом (для Checkbox)
5. **$orderBy**: Задаёт сортування по умолчанию
## Фільтрування по повʼязным моделям
// Через Foreign поле
  ->filter("foreign"); // special type for foreign
## Кастомная сортування
// В Definition
public function getQuery()
    return parent::getQuery()
        ->orderBy('is_active', 'desc')
        ->orderBy('title', 'asc');
## Перегляд доступних Definitions
```bash
php artisan tinker
>>> echo implode(', ', array_keys((new \Linecore\Cms\Tree)->definitions()));
## Управление через адмінку
В адмінці фільтри отображаются автоматически:
- Поля с `->filter()` появляются в строке фільтрів
- Поля с `->sortable()` позволяют сортувать кликом по заголовку
- `->fastEdit()` позволяет редагувати на лету

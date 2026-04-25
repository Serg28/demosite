---
name: cms-custom-field
description: Створення кастомних полів (Custom Fields) у Linecore Builder CMS. Використовуйте, коли стандартних полів недостатньо і потрібна власна логіка рендеру/збереження/валідації. Не використовуйте для простого додавання стандартного поля — для цього є cms-add-field. Ключові тригери: custom field, нестандартное поле, field class.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Створення кастомних полей в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли стандартних полів недостатньо і треба власний клас поля.
- **RU сигнали**: создать кастомна поле, нестандартное поле в cms
- **EN signals**: create custom field class, custom cms field
- **Не використовувати, коли**: не для додавання стандартного Text/Select/Image поля (це cms-add-field).


## Коли використовувати

- Стандартных полей недостаточно
- Нужна сложная логика отображения/валидации
- Потрібно особый UI в форме редагування
- Интеграция с внешними сервисами

## Базова структура кастомного поля

**Розташування класса**: `app/Cms/Fields/CustomFieldName.php`

**Розташування шаблонов**: `resources/views/cms/fields/custom_field_name.blade.php`

```php
<?php
namespace App\Cms\Fields;
use Linecore\Cms\Fields\Field;
class CustomFieldName extends Field
{
  // Кастомные свойства
  protected $customOption = "default";
  // Методы-сеттеры для кастомних опций
  public function customOption($value)
  {
    $this->customOption = $value;
    return $this;
  }
  // Отображение в форме редагування
  public function getFieldForm($definition)
    return view("cms.fields.custom_field_name", [
      "field" => $this,
      "definition" => $definition,
      "value" => $this->getValue(),
    ])->render();
  // Отображение в переліку
  public function getValueForList($definition)
    $value = $this->getValue();
    // Логика обработки значения
    return $value;
}
```

## Приклад кастомного поля

### Приклад: Выбор категорий с иерархией

class CategoryTreeSelect extends Field
protected $maxDepth = 3;
  public function maxDepth($depth)
$this->maxDepth = $depth;
    $categories = \App\Models\Category::where("depth", "<=", $this->maxDepth)
      ->defaultOrder()
      ->get();
    return view("cms.fields.category_tree_select", [
      "categories" => $categories,
    if (!$value) {
return "—";
}
$category = \App\Models\Category::find($value);
return $category ? $category->t("title") : "—";

### Соответствующий Blade шаблон

**Розташування**: `resources/views/cms/fields/category_tree_select.blade.php`

````blade
@php
    $fieldName = $field->getName();
    $fieldId = $field->id;
    $selectedValue = $value;
@endphp
<div class="form-group">
    <label>{{ $field->title }}</label>
    <select name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control">
        <option value="">—</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ $selectedValue == $category->id ? 'selected' : '' }}>
                {{ str_repeat('— ', $category->depth) }} {{ $category->title }}
            </option>
        @endforeach
    </select>
    @if($field->comment)
        <small class="text-muted">{{ $field->comment }}</small>
    @endif
</div>
### Искористувачание в Definition
use App\Cms\Fields\CategoryTreeSelect;
public function fields(): array
    return [
        'Основное' => [
            // ...
            CategoryTreeSelect::make('Родительская категория', 'parent_id')
                ->maxDepth(3)
                ->comment('Выберите категорию'),
        ],
    ];
## Приклад: Поле с AJAX загрузкой
class ProductSearch extends Field
  protected $searchEndpoint = "/admin/products/search";
  public function endpoint($url)
    $this->searchEndpoint = $url;
    return view("cms.fields.product_search", [
    $product = \App\Models\Product::find($value);
    return $product ? $product->t("title") : "—";
## Доступные методы базового Field
### Сеттеры
// Основні
$field->title('Назва')          // Заголовок поля
$field->name('field_name')        // Имя поля в БД
$field->id('field_id')            // HTML ID
$field->value($value)             // Значение
// Визуальные
$field->className('col-md-6')     // CSS классы
$field->comment('Підказка')       // Комментарий
$field->readonly(true)            // Лише читання
// Функциональные
$field->filter()                  // Включить фільтр
$field->sortable()                // Включить сортування
$field->onlyForm()                // Только форма (не в переліку)
$field->onlyList()                // Только перелік (не в форме)
$field->rules(['required'])       // Валидация
### Геттеры
$field->getTitle()                // Получить заголовок
$field->getName()                 // Получить имя поля
$field->getValue()                // Получить текущее значение
$field->isFilter()               // Проверить фільтр
$field->isSortable()             // Проверить сортування
## Важливі нюанси
1. **Наслідування**: Наследуйтесь от `Linecore\Cms\Fields\Field`
2. **Blade шаблон**: створйте представление в `resources/views/cms/fields/`
3. **Кеширование**: Для тяжёлых запитов используйте кеширование
4. **AJAX**: Для AJAX полей используйте существующие ForeignAjax
5. **JS**: Подключайте скрипты через `@push` в blade шаблоне
## Перегляд существующих кастомних полей
```bash
ls -la app/Cms/Fields/
## Приклади из проекта
### CategoryCharacteristic - таблица пов’язаних характеристик
use Linecore\Cms\Fields\Definition;
class CategoryCharacteristic extends Definition
    $field = $this;
    return view(
      "admin::form.fields.definition",
      compact("definition", "field")
    )->render();
  public function getTable($definition, $parseJsonData)
    // Логика отображения таблицы
      "html" => view(
        "cms.fields.category_characteristic_input_definition_table_data",
        compact(...)
      )->render(),
      "count_records" => 0,
### Price - расширенное текстовое поле с ценой
use Linecore\Cms\Fields\Text;
class Price extends Text
    return view("cms.fields.price", compact("definition", "field"))->render();
  public function priceOnsite()
    $kurs = setting("kurs") * 1;
    return round($kurs * $this->getValue());
### ProductCharacteristics - характеристики товара
// Повний приклад в app/Cms/Fields/ProductCharacteristics.php
### CategoryPriceRanges - диапазоны цен
// Повний приклад в app/Cms/Fields/CategoryPriceRanges.php
## Кастомные поля в проекте (полный перелік)
В проекте уже есть множество готовых кастомних полей:
- `app/Cms/Fields/CategoryCharacteristic.php` - таблица пов’язаних характеристик
- `app/Cms/Fields/CategoryPriceRanges.php` - диапазоны цен
- `app/Cms/Fields/ProductCharacteristics.php` - характеристики товара
- `app/Cms/Fields/Price.php` - поле цены
- `app/Cms/Fields/TextExt.php` - расширенное текстовое поле
- `app/Cms/Fields/Tinymce.php` - WYSIWYG редактор
- `app/Cms/Fields/ForeignAjax*.php` - различные AJAX звʼязки
- `app/Cms/Fields/ManyToManyAjax*.php` - множественные звʼязки
## Перегляд существующих полей
````

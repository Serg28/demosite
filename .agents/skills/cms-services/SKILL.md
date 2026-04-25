---
name: cms-services
description: Створення кастомних сервісів (Listing, Actions) в адмінці Linecore Builder CMS. Використовуйте для нестандартного запиту переліку, прав доступу, пагінації та дій. Не використовуйте для створення полів або шаблонів Tree. Ключові тригери: listing service, actions service, сервис переліку.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Кастомные сервисы в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібні кастомні Listing/Actions сервіси.
- **RU сигнали**: кастомний listing, actions service, логика переліку
- **EN signals**: custom listing service, custom actions service
- **Не використовувати, коли**: не для створення полів/Tree-шаблонів.

## Коли використовувати
- Кастомна логіка переліку записів
- Особая пагинация
- Фільтрування по умолчанию
- Додавання условий к запиту
## Listing Service
### Базова структура
**Розташування**: `app/Cms/Services/`
```php
<?php
namespace App\Cms\Services;
use Linecore\Cms\Services\Listing;
class CustomListing extends Listing
{
  public function getQuery()
  {
    $query = parent::getQuery();
    // Кастомна логіка
    if (request("special_filter")) {
      $query->where("special_field", request("special_filter"));
    }
    return $query;
  }
  public function getPerPage()
    return 50;
}
```
### Подключение в Definition
protected $service = CustomListing::class;
## Приклади Listing Service
### Приклад 1: Фільтрування по умолчанию
use App\Models\Product;
class ProductListing extends Listing
    // Показывать только активные товары
    $query->where("is_active", true);
    // Если есть параметр category
    if (request("category_id")) {
      $query->where("category_id", request("category_id"));
### Приклад 2: Кастомная пагинация
class CustomPerPage extends Listing
    // Разные значения для разных ресурсов
    $perPage = request("per_page", 50);
    // Лимит
    return min($perPage, 200);
### Приклад 3: Сложная сортування
class OrderListing extends Listing
    // Сортування по статусу + дате
    $query->orderByRaw("FIELD(status, 'pending', 'processing', 'completed')");
    $query->orderBy("created_at", "desc");
### Приклад 4: С условием прав доступа
use Sentinel;
class UserListing extends Listing
    // Фільтрування по роли
    if (!Sentinel::inRole("admin")) {
      $userId = Sentinel::getUser()->id;
      $query->where("created_by", $userId);
## Actions Service
use Linecore\Cms\Services\Actions;
class CustomActions extends Actions
  public function insert()
    $this->insert = true;
    $this->insertTitle = "Створити";
    $this->insertIcon = "plus";
    return $this;
  public function customAction()
    $this->customAction = true;
    $this->customActionTitle = "Опубликовать";
    $this->customActionIcon = "publish";
### Подключение
public function actions()
    return (new CustomActions())->insert()->update()->delete();
## Повний приклад Definition с сервисами
namespace App\Cms\Definitions;
use App\Cms\Services\ProductListing;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\{Id, Text, Checkbox};
class Products extends Resource
  public $model = Product::class;
  public string $title = "Товары";
  // Подключение кастомного Listing
  protected $service = ProductListing::class;
  protected $orderBy = "id desc";
  protected $perPage = [20, 50, 100];
  public function fields(): array
    return [
      "Основное" => [
        Id::make("#", "id")
          ->sortable()
          ->filter(),
        Text::make("Назва", "title")
          ->language()
          ->filter()
          ->sortable(),
        Checkbox::make("Активен", "is_active")
          ->fastEdit(),
      ],
    ];
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->delete();
## Методы Listing Service
| Метод           | Описание                        |
| --------------- | ------------------------------- |
| `getQuery()`    | Получение и модификация запита |
| `getPerPage()`  | Количество записів на странице  |
| `getOrderBy()`  | Сортування по умолчанию         |
| `applyScopes()` | Применение scope модели         |
## Важливі нюанси
1. **Наслідування**: Всегда вызывайте `parent::getQuery()`
2. **request()**: Використовуйте для получения параметров
3. **Sentinel**: Для проверки прав доступа
4. **where()**: Добавляйте условия к запиту
## CMS Специфичные сервисы
В проекте есть готовые сервисы для заказов и email шаблонов:
### Для заказов
- `ListingOrders.php` - листинг заказов
- `ActionsOrders.php` - дії с заказами
### Для email шаблонов
- `ListingEmailTemplates.php` - листинг email шаблонов
- `ActionsEmailTemplates.php` - дії с email шаблонами
## Перегляд существующих сервисов
```bash
ls -la app/Cms/Services/
## Также в проекте есть
### Buttons (app/Cms/Buttons/, опционально)
- `ClearPageSeoLinks.php` - очистка SEO ссылок
- `RegeneratePageSeoLinks.php` - перегенерация SEO ссылок
### Exports (app/Cms/Exports/)
- `ExportProducts.php` - експорт товаров
- `ExportProductOptions.php` - експорт опций товаров
- `ExportProductStats.php` - експорт статистики товаров
- `UnfinishedBasketsExport.php` - експорт брошенных корзин
### Imports (app/Cms/Imports/)
- `ImportProducts.php` - імпорт товаров
- Другие імпортеры
## Перевизначення методов
  // Переопределить получение записів
    return parent::getQuery()->with(["category", "brand"]);
  // Переопределить подготовку даних для переліку
  public function prepareFields($row, $definitions)
    $fields = parent::prepareFields($row, $definitions);
    // Додати кастомную логику
    foreach ($fields as $key => $field) {
      if ($key === "price") {
        $fields[$key]["value"] = number_format($field["value"], 2);
      }
    return $fields;

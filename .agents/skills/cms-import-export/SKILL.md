---
name: cms-import-export
description: Додавання імпорту та експорту даних в адмінку Linecore Builder CMS. Використовуйте для CSV/Excel завантаження, валідації імпорту та формування вивантажень. Не використовуйте для звичайного CRUD без файлів. Ключові тригери: import, export, excel, csv, выгрузка.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Імпорт и експорт в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли треба імпорт/експорт через Excel/CSV.
- **RU сигнали**: імпорт excel, експорт csv, загрузка файла в админке
- **EN signals**: excel import, csv export, admin data import export
- **Не використовувати, коли**: не для звичайного CRUD без файлів.

## Коли використовувати
- Користувач просить додати імпорт даних
- Нужен експорт в Excel/CSV
- Потрібно загрузка/выгрузка даних из файлів
## Структура імпорта
**Розташування**: `app/Cms/Imports/`
```php
<?php
namespace App\Cms\Imports;
use App\Models\YourModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class ImportYourModel implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    return new YourModel([
      "title" => $row["title"],
      "code" => $row["code"],
      "price" => $row["price"],
      "is_active" => $row["is_active"] ?? 1,
    ]);
  }
}
```
## Структура експорта
**Розташування**: `app/Cms/Exports/`
namespace App\Cms\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportYourModel implements FromCollection, WithHeadings
  public function collection()
    return YourModel::select(
      "id",
      "title",
      "code",
      "price",
      "is_active"
    )->get();
  public function headings(): array
    return ["ID", "Назва", "Код", "Цена", "Активен"];
## Подключение в Definition
use App\Cms\Imports\ImportYourModel;
use App\Cms\Exports\ExportYourModel;
class YourModelDefinition extends Resource
  // ...
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->delete()
      ->import(ImportYourModel::class) // Імпорт
      ->export(ExportYourModel::class); // Експорт
## Приклад: Імпорт товаров
use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithValidation;
class ImportProducts implements ToModel, WithHeadingRow, WithValidation
    // Поиск существующего товара по коду
    $product = Product::where("code", $row["code"])->first();
    if ($product) {
      // Обновление
      $product->update([
        "title" => $row["title"],
        "price" => $row["price"],
      ]);
      return null; // Не створём новую модель
    }
    // Створення нового
    return new Product([
      "slug" => \Str::slug($row["title"]),
  public function rules(): array
    return [
      "code" => "required|unique:products,code",
      "title" => "required",
      "price" => "required|numeric",
    ];
## Приклад: Експорт товаров
use Maatwebsite\Excel\Concerns\WithMapping;
class ExportProducts implements FromCollection, WithHeadings, WithMapping
    return Product::with("category")->get();
  public function map($product): array
      $product->id,
      $product->code,
      $product->t("title"),
      $product->category?->t("title"),
      $product->price,
      $product->is_active ? "Да" : "Нет",
    return ["ID", "Код", "Назва", "Категория", "Цена", "Активен"];
## Пакеты
Проект использует `maatwebsite/excel`:
- Laravel Excel для імпорта/експорта
## Важливі нюанси
1. **Heading Row**: Використовуйте `WithHeadingRow` для роботи с заголовками из первой строки
2. **Валидация**: Реализуйте `WithValidation` для проверки даних
3. **Relations**: Для експорта пов’язаних даних используйте `with()` в query
4. **Large Data**: Для больших объёмов используйте `ChunkedReader`
## Команди
```bash
# Тестирование імпорта
php artisan tinker
$import = new \App\Cms\Imports\ImportProducts;
$import->import('file.xlsx');

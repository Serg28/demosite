---
name: cms-actions
description: Налаштування дій (Actions) в адмінці Linecore Builder CMS. Використовуйте цей навик, коли потрібно додати/прибрати дії, змінити назви або іконки кнопок у розділі. Не використовуйте для створення кастомних кнопок з окремим класом — для цього є cms-buttons. Ключові тригери: actions, дії, кнопки, операции.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Налаштування Actions в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли треба керувати набором дій у Definition (insert/update/delete/clone/import/export).
- **RU сигнали**: налаштувати actions, действия раздела, убрать/добавить кнопки действий
- **EN signals**: configure actions, definition actions, enable disable default actions
- **Не використовувати, коли**: не для окремих класів кнопок (це cms-buttons).


## Коли використовувати

- Налаштування доступних дій у розділі адмінки
- Додавання/видалення кнопок (створити, редагувати, видалити)
- Зміна названий и иконок дій
- Додавання кастомних дій

## Базове використання

```php
use Linecore\Cms\Services\Actions;
public function actions()
{
    return Actions::make()
        ->insert()           // Створити
        ->update()           // Редагувати
        ->clone()            // Клонувати
        ->revisions()        // Історія змін
        ->delete()           // Видалити
        ->import()           // Імпорт
        ->export()           // Експорт
        ->customAction();    // Кастомна дія
}
```

## Стандартні дії

### insert() - Створення

public function insert()
$this->insert = true;
$this->insertTitle = 'Створити новий елемент';
$this->insertIcon = 'plus';
return $this;

### update() - Редагування

public function update()
$this->update = true;
$this->updateTitle = 'Редагувати';
$this->updateIcon = 'pencil';

### delete() - Видалення

public function delete()
$this->delete = true;
$this->deleteTitle = 'Видалити';
$this->deleteIcon = 'trash';
$this->deleteMessage = 'Ви впевнені?';

### clone() - Клонирование

public function clone()
$this->clone = true;
$this->cloneTitle = 'Клонувати';
$this->cloneIcon = 'copy';

### revisions() - Історія змін

public function revisions()
$this->revisions = true;
$this->revisionsTitle = 'Історія змін';
$this->revisionsIcon = 'history';

### import() - Імпорт

public function import()
$this->import = true;
$this->importTitle = 'Імпорт';
$this->importIcon = 'upload';
$this->importClass = ImportYourModel::class;

### export() - Експорт

public function export()
$this->export = true;
$this->exportTitle = 'Експорт';
$this->exportIcon = 'download';
$this->exportClass = ExportYourModel::class;

## Кастомна дія

public function customAction()
$this->customAction = true;
$this->customActionTitle = 'Опубликовать';
$this->customActionIcon = 'publish';
$this->customActionModel = CustomActionModel::class;

## Приклади

### Мінімальний набор

        ->insert()
        ->update()
        ->delete();

### Повний набір

        ->clone()
        ->revisions()
        ->delete()
        ->import(ImportProducts::class)
        ->export(ExportProducts::class);

### Лише читання

    return Actions::make();
    // Жодних дій

## Важливі нюанси

1. **Порядок**: Дії відображаються в том порядке, в котором добавлены
2. **Права**: Перевіряйте права доступа перед выполнением
3. **Иконки**: Використовуйте доступні иконки (pencil, trash, copy, plus, etc.)
4. **Подтверждение**: Для delete можна налаштувати сообщение подтверждения

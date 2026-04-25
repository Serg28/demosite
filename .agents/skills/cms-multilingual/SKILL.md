---
name: cms-multilingual
description: Налаштування мультимовності у Linecore Builder CMS. Використовуйте для мов сайту, перекладних полів, helpers перекладу та адмін-перекладів. Не використовуйте для локалізації фронтенд-компонента поза CMS-контекстом. Ключові тригери: multilingual, i18n, переклади, language().
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Мультиязычность в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли треба налаштувати мови, перекладні поля та helper-переклади.
- **RU сигнали**: мультиязычность, переводы, language поля
- **EN signals**: multilingual, translations, translatable fields
- **Не використовувати, коли**: не для загальної i18n логіки поза CMS-контекстом.

## Коли використовувати
- Налаштування языков сайта
- Додавання мультиязычных полей в Definition
- Налаштування перекладов интерфейса
- Работа с перекладами в коде
## Налаштування языков
### Конфигурация языков
**Файл**: `config/cms/translations/config.php`
```php
return [
  "uk" => "Українська",
  "ru" => "Русский",
  "en" => "English",
];
```
### Получение языков в коде
// Мова за замовчуванням
$defaultLang = defaultLanguage();
// Все языки сайта
$languages = languagesOfSite();
// Текущий язык приложения
$currentLang = app()->getLocale();
## Мультиязычные поля в Definition
### Text с мультиязычностью
use Linecore\Cms\Fields\Text;
Text::make("Назва", "title")
  ->language()
  ->filter()
  ->sortable()
  ->transliteration("slug", true); // Автотранслитерация slug
### Textarea с мультиязычностью
use Linecore\Cms\Fields\Textarea;
Textarea::make("Описание", "description")
  ->rows(5);
### Tinymce с мультиязычностью
use Linecore\Cms\Fields\Tinymce;
Tinymce::make("Содержание", "content")->language();
### Image с мультиязычностью
use Linecore\Cms\Fields\Image;
Image::make("Зображення", "picture")->language();
## Параметры мультиязычных полей
### language() - Основной параметр
// Включить мультиязычность
->language()
### autoTranslate() - Автопереклад
// Автоматический переклад при сохранении
  ->autoTranslate(true);
### transliteration() - Транслитерация
// Автоматическая генерация slug из title
  ->transliteration("slug", true); // true - уникальный slug
## Переклады в коде
### \_\_cms() - Переклад для CMS
// Получение переклада по ключу
$translated = __cms("key");
// С параметрами
$translated = __cms("Hello {name}!", ["name" => "User"]);
### trans_choice() - Множественные формы
$count = 5;
$text = trans_choice(
  "{0} нет товаров|{1} товар|[2,4] товара|[5,*] товаров",
  $count
);
### Метод t() модели
// Получение переведенного значения
$title = $product->t("title");
// С указанием языка
$titleUk = $product->t("title", "uk");
## Переклады в Blade шаблонах
```blade
{{ __t('Назва') }}
{{ __t('Выбрано [count] товаров', ['[count]' => $count]) }}
{{ trans_choice(__t('{0} товаров|[1] товар|[2,4] товара|[5,*] товаров'), $count) }}
## SEO поля с мультиязычностью
use App\Models\MorphOne\Seo;
public function fields(): array
{
    return [
        // ...
        'SEO' => Seo::fieldsForDefinitions($this->model),
    ];
}
## Приклад Definition с мультиязычностью
<?php
namespace App\Cms\Definitions;
use App\Models\Article;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\{Text, Textarea, Tinymce, Checkbox};
use Linecore\Cms\Services\Actions;
class Articles extends Resource
  public $model = Article::class;
  public string $title = "Статьи";
  public function fields(): array
  {
      "Основное" => [
        Text::make("Заголовок", "title")
          ->language()
          ->filter()
          ->sortable()
          ->transliteration("slug", true),
        Text::make("Slug", "slug")
          ->onlyForm(),
        Textarea::make("Краткое опис", "short_description")->language(),
        Tinymce::make("Содержание", "content")->language(),
      ],
      "Настройки" => [
        Checkbox::make("Опубликовано", "is_active")
          ->fastEdit(),
      "SEO" => \App\Models\MorphOne\Seo::fieldsForDefinitions($this->model),
  }
  public function actions()
    return Actions::make()
      ->insert()
      ->update()
      ->delete();
## Важливі нюанси
1. **Хранение**: Переклады хранятся в JSON формате в таблице translations
2. **Поле language()**: Добавляет суффикс языка к имени поля в БД (title_uk, title_ru)
3. **SEO**: Використовуйте `Seo::fieldsForDefinitions()` для SEO-полей
4. **defaultLanguage()**: Всегда возвращает мова за замовчуванням
5. **fallback**: При отсутствии переклада используется мова за замовчуванням
## Функции для роботи
| Функция              | Описание              |
| -------------------- | --------------------- |
| `defaultLanguage()`  | Мова за замовчуванням     |
| `languagesOfSite()`  | Все языки сайта       |
| `__cms('key')`       | Переклад для CMS       |
| `__t('key')`         | Переклад для фронтенда |
| `$model->t('field')` | Переклад поля модели   |
## Перегляд перекладов
```bash
# Перейдите в адмінці
/admin/translations/phrases
/admin/translations_cms/phrases
## Приклад роботи с перекладами
// Получение всех перекладов для статьи
$article = Article::find(1);
// Получить заголовок на текущем языке
$title = $article->t("title");
// Получить на конкретном языке
$titleUk = $article->getTranslation("title", "uk");
$titleRu = $article->getTranslation("title", "ru");
// Установить переклад
$article->setTranslation("title", "uk", "Новий заголовок");
$article->save();

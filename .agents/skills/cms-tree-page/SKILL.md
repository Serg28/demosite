---
name: cms-tree-page
description: Створення деревовидних сторінок (Tree) у Linecore Builder CMS. Використовуйте для шаблонів вузлів, ієрархії сторінок і налаштування tree-структури. Не використовуйте для звичайних CRUD Definition. Ключові тригери: tree page, дерево страниц, resource tree, шаблон сторінки.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Створення Tree страниц в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібно створити/налаштувати ієрархічні Tree-сторінки.
- **RU сигнали**: tree страницы, дерево страниц, шаблон tree
- **EN signals**: tree pages, hierarchical pages, resource tree template
- **Не використовувати, коли**: не для звичайного Definition CRUD.

## Коли використовувати
- Користувач просить створити новую страницу с деревом (древовидная навигация)
- Потрібно додати новий тип страницы (статья, новость, контакты, etc.)
- Потрібно налаштувати шаблон вывода страницы
- Створення страниц с иерархической структурой
## Базова структура
### Главный файл Tree
**Розташування**: `app/Cms/Tree/Tree.php`
```php
<?php
namespace App\Cms\Tree;
use App\Cms\Tree\Templates\{Article, News, Main};
use Linecore\Cms\Definitions\BaseTree;
class Tree extends BaseTree
{
  public function templates()
  {
    return [
      "main" => Main::class,
      "contacts" => Contacts::class,
      "article" => Article::class,
      "news" => News::class,
      // Добавляем новий шаблон
      "custom" => CustomTemplate::class,
    ];
  }
}
```
### Шаблон страницы
**Розташування**: `app/Cms/Tree/Templates/`
namespace App\Cms\Tree\Templates;
use Linecore\Cms\Definitions\ResourceTree;
use Linecore\Cms\Fields\{Text, Image, Checkbox, Tinymce};
class CustomTemplate extends ResourceTree
  public $action = "CustomController@index";
  protected $titleDefinition = "Назва типа";
  public function fields()
      "Основное" => [
        Text::make("Заголовок", "title")->language(),
        Text::make("Slug", "slug")->rules(["required", "unique:tb_tree"]),
        Tinymce::make("Содержание", "content")->language(),
        Image::make("Зображення", "image"),
        Checkbox::make("Активно", "is_active"),
      ],
      "SEO" => [
        Text::make("Meta Title", "meta_title")->language(),
        Text::make("Meta Description", "meta_description")->language(),
## Доступные поля для Tree
Все стандартные поля Resource + специфичные для Tree:
use Linecore\Cms\Fields\{Text, Image, Checkbox, Tinymce, Foreign};
// Основні поля
Text::make('Заголовок', 'title')->language()
Tinymce::make('Содержание', 'content')->language()
Image::make('Зображення', 'image')
Checkbox::make('Активно', 'is_active')
// Поля для пов’язывания с меню
Foreign::make('Розділ меню', 'menu_id')
    ->options((new Options('menu'))->isJson())
## Приклади шаблонов
### Статья
use Linecore\Cms\Fields\{Text, Image, Tinymce, Checkbox};
class Article extends ResourceTree
  public $action = "ArticleController@index";
  protected $titleDefinition = "Статья";
### Новость
use Linecore\Cms\Fields\{Text, Image, Tinymce, Checkbox, Date};
class News extends ResourceTree
  public $action = "NewsController@index";
  protected $titleDefinition = "Новость";
        Date::make("Дата публикации", "publish_at"),
### Контакты
use Linecore\Cms\Fields\{Text, Textarea, Checkbox};
class Contacts extends ResourceTree
  public $action = "ContactsController@index";
  protected $titleDefinition = "Контакты";
        Text::make("Телефон", "phone")->language(),
        Text::make("Email", "email")->language(),
        Textarea::make("Адрес", "address")->language(),
        Checkbox::make("Показать форму обратной звʼязки", "show_contact_form"),
## Подключение шаблона
1. Добавьте шаблон в `app/Cms/Tree/Tree.php`
2. Зарегистрируйте роут контроллера (если нужен)
3. створйте представление (view) для вывода
4. В адмінці выберите тип страницы в налаштуваннях
## Controller для Tree
namespace App\Http\Controllers;
class CustomController extends Controller
  public function index($url = null)
    $page = \App\Models\Tree::where("slug", $url)->firstOrFail();
    $page->setSeoGroups("page.index");
    return view($page->getTemplate(), ["page" => $page]);
## Важливі нюанси
1. **URL таблицы**: Используется таблица `tb_tree` (наследуется от Linecore\Cms\Models\Tree)
2. **Поле slug**: Обязательно для формирования URL страницы
3. ** multilingual**: Використовуйте `->language()` для полей с перекладами
4. **Иерархия**: TreeBuilder использует NestedSet (lft, rgt, parent_id, depth)
5. **Кеширование**: Використовуйте `getCacheTags()` для инвалидации
## Доступные шаблоны страниц в проекте
В проекте уже есть множество готовых шаблонов:
```bash
ls app/Cms/Tree/Templates/
Перелік доступних шаблонов:
- `Main` - Главная страница
- `Contacts` - Контакты
- `Article` - Статья
- `News` - Новость
- `Faq` - FAQ
- `About` - О нас
- `Delivery` - Доставка
- `Offer` - Оferta
- `Politic` - Политика конфиденциальности
- `Universal` - Универсальная страница
- `OrderStatus` - Статус заказа
- `OrderParts` - Заказ запчастей
- `Vendors` - Бренды
- `Models` - Модели
- `Vacancies` - Вакансии
- `SpecialOffers` - Специальные предложения
- `OptovymClient` - Оптовый клиент
- `ReviewsIndex` - Отзывы (индекс)
- `ReviewsProducts` - Отзывы о товарах
- `ReviewsCompany` - Отзывы о компании
## Приклад из проекта: Article
use App\Cms\Definitions\Blocks;
use App\Cms\Fields\Tinymce;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Definition;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Image;
use Linecore\Cms\Fields\Text;
      "Общее" => [
        Id::make("#", "id")->sortable(),
        Tinymce::make("Описание", "description")->language(),
        Text::make("Slug (old url)", "slug"),
        Text::make("Url", "url")
          ->language()
          ->filter()
          ->sortable()
          ->rules(["required", Rule::unique("tb_tree")->ignore(request("id"))])
          ->onlyForm(),
        Image::make("Картинка", "picture"),
      "Блоки" => [
        Definition::make("Блоки")->morphMany("blocks", Blocks::class),
      "SEO" => Seo::fieldsForDefinitions(),
## Налаштування Tree в Admin.php
Меню "Структура сайта" автоматически добавляется в адмінку:
// В app/Cms/Admin.php
public function menu()
        [
            'title' => 'Структура сайта',
            'icon' => 'sitemap',
            'link' => '/tree',
        ],
## Команди
# Очистка кеша дерева
php artisan cache:clear

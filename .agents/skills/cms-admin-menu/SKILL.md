---
name: cms-admin-menu
description: Налаштування меню адмінки та конфігурації Admin у Linecore Builder CMS. Використовуйте, коли потрібно змінити структуру меню, пункти, іконки, Login-сторінку або брендинг адмінки. Не використовуйте для налаштування полів Definition. Ключові тригери: menu, меню, admin config, навигация адмінки.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Налаштування меню адмінки в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібно змінити меню/брендинг/логін адмінки.
- **RU сигнали**: налаштувати меню адмінки, изменить пункты меню, admin menu
- **EN signals**: admin menu config, customize admin navigation, admin branding
- **Не використовувати, коли**: не для полів та логіки конкретного Definition.

## Коли використовувати
- Налаштування структуры меню адмінки
- Додавання новых розділов в меню
- Налаштування иконок и порядка
- Конфигурация страницы входа (Login)
## Admin Configuration
### Розташування
**Файл**: `app/Cms/Admin.php`
```php
<?php
namespace App\Cms;
use Linecore\Cms\Setting\AdminBase;
class Admin extends AdminBase
{
  // Настройки
}
```
## Основні налаштування Admin
### Логотип и Favicon
protected $logoUrl = '/cms_files/logo.svg';
protected $faviconUrl = '/cms_files/favicon.ico';
### CSS и JS
protected $css = [
    '/packages/linecore/cms/custom.css',
];
protected $js = [
    '/packages/linecore/cms/custom.js',
### Login страница
public function login()
    return Login::class;  // Указывает на класс Login
## Структура меню
### Базовый елемент меню
[
  "title" => "Назва пункта",
  "icon" => "icon-name", // Иконка
  "link" => "/route", // URL
### Меню с подменю
  "title" => "Каталог",
  "icon" => "box",
  "link" => "/catalog",
  "submenu" => [
    [
      "title" => "Товары",
      "link" => "/products",
    ],
      "title" => "Категории",
      "link" => "/categories",
  ],
### Повний приклад меню
public function menu()
    return [
        [
            'title' => 'Робочий стіл',
            'icon' => 'chart-line',
            'link' => '/dashboard',
        ],
            'title' => 'Структура сайта',
            'icon' => 'sitemap',
            'link' => '/tree',
            'title' => 'Меню',
            'icon' => 'chart-network',
            'link' => '/menu',
            'submenu' => [
                [
                    'title' => 'Меню Хедер',
                    'link' => '/menu_header',
                ],
                    'title' => 'Меню Футера',
                    'link' => '/menu_footer',
            ],
            'title' => 'Товары',
            'icon' => 'box',
            'link' => '/products',
                    'title' => 'Все товары',
                    'link' => '/products',
                    'title' => 'Категории',
                    'link' => '/categories',
                    'title' => 'Бренды',
                    'link' => '/brands',
    ];
## Доступные иконки
Иконки из библиотеки (обычно Font Awesome или подобные):
- `chart-line` - график
- `sitemap` - структура
- `box` - товары
- `shopping-cart` - заказы
- `building` - новости
- `briefcase` - вакансии
- `users` - користувачатели
- `cog` - налаштування
## Налаштування Login
**Файл**: `app/Cms/Login.php`
use Linecore\Cms\Setting\Login as LoginBase;
class Login extends LoginBase
  protected $backgroundUrl = "/cms_files/login-bg.jpg";
  public function onLogin()
  {
    return redirect("/admin/dashboard");
  }
### Возможности Login
  // Фон страницы входа
  protected $backgroundUrl = "/cms_files/admin-lock.jpg";
  // Редирект після входа
  // Кастомна логіка
  public function authenticate($credentials)
    // Ваша логика аутентификации
    return true; // или false
## Приклади конфигурации
### Приклад 1: Минимальная конфигурация
  protected $logoUrl = "/cms_files/logo-w.svg";
  public function menu()
      [
        "title" => "Робочий стіл",
        "icon" => "chart-line",
        "link" => "/dashboard",
      ],
        "title" => "Товары",
        "icon" => "box",
        "link" => "/products",
### Приклад 2: Сложное меню
  protected $faviconUrl = "/cms_files/favicon.ico";
  protected $css = ["/packages/linecore/cms/custom.css"];
  protected $js = ["/packages/linecore/cms/app.js"];
  public function login()
    return Login::class;
      // Главная
      // Структура сайта
        "title" => "Структура сайта",
        "icon" => "sitemap",
        "link" => "/tree",
      // Меню
        "title" => "Меню",
        "icon" => "chart-network",
        "link" => "/menu_header",
        "submenu" => [
          [
            "title" => "Меню Хедер",
            "link" => "/menu_header",
          ],
            "title" => "Меню Футера",
            "link" => "/menu_footer",
            "title" => "Меню Каталог",
            "link" => "/menu_catalog",
      // Контент
        "title" => "Контент",
        "icon" => "file-alt",
        "link" => "/news",
            "title" => "Новости",
            "link" => "/news",
            "title" => "Теги",
            "link" => "/tags",
      // Каталог
        "title" => "Каталог",
            "title" => "Товары",
            "link" => "/products",
            "title" => "Категории",
            "link" => "/categories",
      // Заказы
        "title" => "Заказы",
        "icon" => "shopping-cart",
        "link" => "/orders",
            "title" => "Все заказы",
            "link" => "/orders",
            "title" => "Статусы",
            "link" => "/orders_status",
      // Настройки
        "title" => "Настройки",
        "icon" => "cog",
        "link" => "/settings",
## URL адмінки
- Главная: `/admin` или `/admin/dashboard`
- Розділы: `/admin/{resource-name}`
- Tree: `/admin/tree`
- Настройки: `/admin/settings`
- Переклады: `/admin/translations/phrases` и `/admin/translations_cms/phrases`
## Важливі нюанси
1. **URL розділов**: Формируется автоматически из названия Definition
2. **Иконки**: Використовуйте существующие в библиотеке
3. **submenu**: До 2-х уровней вложенности
4. **Login**: Наследуйтесь от `Login as LoginBase`
## Проверка меню
```bash
# Очистка кеша
php artisan cache:clear
## Полезные ссылки
- Definitions: `app/Cms/Definitions/`
- Tree Templates: `app/Cms/Tree/Templates/`
- Buttons: `app/Cms/Buttons/` (может отсутствовать в проекте, створйте при необходимости)

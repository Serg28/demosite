# Хлібні крихти (Breadcrumbs)

## Архітектура

```
BreadcrumbsService          → будує list<BreadcrumbItem>
ViewComposers               → передають $breadcrumbs у view
<x-breadcrumbs>             → рендер Schema.org BreadcrumbList
```

## Класи

### `App\ValueObjects\BreadcrumbItem`
```php
readonly class BreadcrumbItem {
    public function __construct(
        public string $title,
        public string $url = '',  // порожньо = поточна сторінка (без <a>)
    ) {}
}
```

### `App\Services\BreadcrumbsService`

| Метод | Коли використовувати |
|---|---|
| `forCategory(Category)` | Сторінка каталогу/категорії |
| `forFilteredCategory(Category, string $filterTitle)` | SEO filter page (Phase 3.7) |
| `forCharacteristic(Characteristic, string $valueName)` | Сторінка значення характеристики |
| `forBrand(string $brandName, ?string $brandsUrl)` | Сторінка бренду |
| `forProduct(Product)` | Картка товару (Головна → Категорія → Товар) |
| `forProfile(string $pageTitle)` | Сторінки особистого кабінету |
| `simple(string $currentTitle, array $pages)` | Статичні сторінки (доставка, про нас) |

### ViewComposers

| Клас | Реєструється для | Очікує у view |
|---|---|---|
| `BreadcrumbsCategoryComposer` | `catalog.index` | `$category`, опційно `$filterTitle` |
| `BreadcrumbsSimpleComposer` | статичні views | `$breadcrumbTitle`, `$pages[]` |
| `BreadcrumbsCharacteristicComposer` | characteristic views | `$characteristic`, `$valueName` |
| `BreadcrumbsBrandComposer` | brand views | `$brandName`, `$brandsUrl` |

## Використання

### Реєстрація (AppServiceProvider)
```php
View::composer('catalog.index', BreadcrumbsCategoryComposer::class);
```

### У Blade-шаблоні
```blade
<x-breadcrumbs :items="$breadcrumbs ?? []" />
```

### Вручну (в контролері або тесті)
```php
$items = app(BreadcrumbsService::class)->forCategory($category);
```

### Патерн для контролерів — ОБОВ'ЯЗКОВО

Кожен контролер, що рендерить view зі шаблоном, приймає `BreadcrumbsService` через DI і передає `$breadcrumbs` у view:

```php
use App\Services\BreadcrumbsService;

// Профіль — прості сторінки
public function addresses(BreadcrumbsService $breadcrumbs): View
{
    return view('profile.addresses', [
        'breadcrumbs' => $breadcrumbs->forProfile(__t('Адреси доставки')),
        'action'      => 'addresses',
    ]);
}

// Профіль — головна (без $pageTitle)
public function index(BreadcrumbsService $breadcrumbs): View
{
    return view('profile.index', [
        'breadcrumbs' => $breadcrumbs->forProfile(),
        'action'      => 'index',
    ]);
}

// Замовлення — вкладена сторінка
public function ordersDetails(int $orderId, BreadcrumbsService $breadcrumbs): View
{
    return view('profile.order', [
        'breadcrumbs' => $breadcrumbs->simple(
            __t('Замовлення #') . $orderId,
            [
                route('profile.index')  => __t('Кабінет'),
                route('profile.orders') => __t('Мої замовлення'),
            ]
        ),
    ]);
}

// Каталог — через ViewComposer (автоматично, не треба передавати вручну)
// Товар — через BreadcrumbsProductComposer або вручну:
public function show(Product $product, BreadcrumbsService $breadcrumbs): View
{
    return view('product.show', [
        'breadcrumbs' => $breadcrumbs->forProduct($product),
    ]);
}
```

**Правило**: `$breadcrumbs` завжди передається у view. У шаблоні завжди `<x-breadcrumbs :items="$breadcrumbs ?? []" />`. Ніколи не хардкодити `<nav>` або HTML-посилання для крихт.

## SEO

- **Schema.org**: `BreadcrumbList` + `ListItem` + `itemprop="position"`
- **Остання крихта категорії**: `getSeoBreadcrumbTitle()` = `seo_h1` без `{{pagenumber}}`, fallback → `title`
- **HasSeo trait**: метод `getSeoBreadcrumbTitle()` доступний на всіх моделях з `HasSeo`

## Дерево категорій

- `Category` використовує `Kalnoy\Nestedset\NodeTrait` (колонки `lft`/`rgt`)
- `forCategory()` — **один SQL-запит** `WHERE rgt BETWEEN lft AND rgt`, не N+1
- Кеш ancestors: `cache()->remember("breadcrumbs.category.{id}", 3600)`
- Інвалідувати при зміні дерева: `cache()->forget("breadcrumbs.category.{id}")`

## Відповідність linecore-demo

| demo.loc | linecore-demo |
|---|---|
| `BreadcrumbsService` | `Services/Breadcrumbs` + ViewComposers |
| `BreadcrumbItem` VO | масив `['url'=>'...', 'title'=>'...']` |
| `NodeTrait` на Category | `Tree::getAncestorsAndSelf()` |
| `getSeoBreadcrumbTitle()` в HasSeo | `SeoTrait::getSeoBreadcrumbTitle()` |

## TODO (незавершено)

- 🔲 **Зареєструвати composers** в `AppServiceProvider` — зараз тільки `BreadcrumbsCategoryComposer` зареєстровано. `BreadcrumbsSimpleComposer`, `BreadcrumbsCharacteristicComposer`, `BreadcrumbsBrandComposer` — код є, реєстрація потрібна при створенні відповідних шаблонів (бренди, характеристики, статичні сторінки).
- 🔲 **Крихти для сторінки товару** (Phase 5) — потрібен `BreadcrumbsService::forProduct(Product, Category)` + реєстрація Composer на `pages.product`. Аналог `BreadcrumbsComposer` з linecore-demo (там через CMS Tree, тут — через Category ancestors).
- 🔲 **Перенести Tree + крошки для Tree-сторінок** — у linecore-demo `BreadcrumbsComposer` обробляє будь-яку CMS Tree-сторінку через `$view->page->getNode()`. В demo.loc потрібно портувати `Tree` модель (якщо не зроблено) і реалізувати `BreadcrumbsService::forTree(Tree $page, array $extraPages)` + відповідний ViewComposer для статичних CMS Tree-сторінок (доставка, про нас, блог тощо). Реєструвати на всіх `pages.*` view-шаблонах де немає спеціального Composer..

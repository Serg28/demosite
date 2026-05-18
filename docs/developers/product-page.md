# Картка товару — Phase 5.1

> Статус: ❌ не реалізовано | Наступна CRITICAL фаза  
> Референс: `linecore-demo/app/Livewire/Product/`

## Що потрібно зробити

Повна картка товару: маршрут, контролер, Livewire-компоненти, views.

## Поточний стан в demo.loc

- `Product` модель — є (`app/Models/Product.php`)
- `Product.cardFields`, `scopeActive` — є
- `Cart/Addtocart` Livewire компонент — є (Phase 4.1)
- `ImageGallery` — є частково (окремо)
- **Немає**: `ProductController`, маршрут `/product/{slug}`, `Product/Page`, `Product/Gallery`, `Product/Similar`, `Product/CounterQty`

## Архітектура

### Маршрути

```php
// routes/web.php
Route::middleware(['locale'])->group(function () {
    Route::get('/product/{product:slug}', [ProductController::class, 'show'])
        ->name('product.show');
});
```

### ProductController

```php
namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_if(! $product->is_active, 404);

        return view('product.show', compact('product'));
    }
}
```

### Livewire-компоненти

| Компонент | Файл | Опис |
|-----------|------|------|
| `Product\Page` | `app/Livewire/Product/Page.php` | Обгортка сторінки (SEO, breadcrumbs) |
| `Product\Gallery` | `app/Livewire/Product/Gallery.php` | Галерея зображень + відео |
| `Product\CounterQty` | `app/Livewire/Product/CounterQty.php` | Лічильник кількості |
| `Product\Similar` | `app/Livewire/Product/Similar.php` | Схожі товари |

### Структура файлів для реалізації

```
app/
├── Http/Controllers/ProductController.php
└── Livewire/Product/
    ├── Page.php
    ├── Gallery.php
    ├── CounterQty.php
    └── Similar.php

resources/views/
├── product/
│   └── show.blade.php              # Layout + @livewire компоненти
└── livewire/product/
    ├── page.blade.php
    ├── gallery.blade.php
    ├── counter-qty.blade.php
    └── similar.blade.php

tests/Feature/
└── Product/
    ├── ProductPageTest.php
    ├── ProductGalleryTest.php
    ├── CounterQtyTest.php
    └── ProductSimilarTest.php
```

## Деталі компонентів

### Product\Gallery

```php
class Gallery extends Component
{
    public int $id;
    private Product $product;

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->product = Product::findOrFail($id);
    }

    public function rendered(): void
    {
        $this->dispatch('product-slider-initialized');
    }

    // render() → 'livewire.product.gallery'
    // Props: page, otherPictures, video, videoCount
}
```

- `other_pictures` — JSON масив URL (cast у моделі)
- `link_to_youtube` — JSON масив відео (cast у моделі)
- Після рендеру → диспатчить `product-slider-initialized` (для Alpine/JS ініціалізації слайдера)

### Product\CounterQty

```php
class CounterQty extends Component
{
    public int|float $quantity = 1;
    public int|float $min = 1;
    public int|float $max = 1;  // = product.quantity

    public function increment(): void { ... $this->dispatch('product-input-quantity-set', quantity: $this->quantity)->to(Addtocart::class); }
    public function decrement(): void { ... same ... }
    public function updatedQuantity(): void { ... clamp + dispatch ... }
}
```

- Комунікація з `Cart\Addtocart` через `dispatch(...)->to(Addtocart::class)`
- `max` = поточна кількість на складі (`product->quantity`)

### Product\Similar

```php
class Similar extends Component
{
    private Collection $list;

    public function mount(Product $product): void
    {
        $this->list = $product->interestingProducts()
            ->active()
            ->limit(12)
            ->get()
            ->shuffle();
    }
}
```

- Потребує зв'язку `interestingProducts` на моделі Product (BelongsToMany через `products_interesting_products`)

### Product\Page

```php
class Page extends Component
{
    public function render()
    {
        return view('livewire.product.page');
    }
}
```

Обгортка — SEO-мета, breadcrumbs, структуровані дані.

## Модель Product — що потрібно перевірити/додати

```php
// Потенційно відсутні зв'язки:
public function interestingProducts(): BelongsToMany
{
    return $this->belongsToMany(Product::class, 'products_interesting_products', 'product_id', 'similar_id');
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function images(): HasMany
{
    return $this->hasMany(ProductImage::class);
}
```

## Доставки на картці товару

Блок "Доставка" — статичний або через CheckoutDeliveryService:

```php
// В ProductController або Livewire:
$deliveries = CheckoutDeliveryService::forCity(null); // без вибору міста — загальні
```

## SEO

```php
// В ProductController або Product\Page
Seo::setTitle($product->title);
Seo::setDescription($product->short_description);
Seo::setImage($product->picture);
```

## Breadcrumbs

```
Головна → {Категорія} → {Назва товару}
```

```php
BreadcrumbsService::add(route('catalog'), 'Каталог');
BreadcrumbsService::add(route('catalog', $product->category->slug), $product->category->title);
BreadcrumbsService::add(null, $product->title);
```

## Порядок реалізації

1. `ProductController` + маршрут + `product/show.blade.php` (скелет)
2. `Product\Page` + SEO + breadcrumbs
3. `Product\Gallery` + blade (carousel Alpine)
4. `Product\CounterQty` + інтеграція з `Cart\Addtocart`
5. `Product\Similar` + blade (слайдер)
6. Тести (всі 4 групи)

---

## Для адміністратора

### Як додати товар

Через CMS (`/admin/products`):
- Назва (ua/ru/en)
- Опис, коротко
- Ціна + стара ціна
- Фото (основне + галерея)
- Slug — генерується автоматично
- Категорія
- Кількість (склад)
- Активний

### URL картки товару

```
/product/{slug}
```

Приклад: `/product/iphone-15-pro-256gb`

### Схожі товари

Визначаються через прив'язку `products_interesting_products` в БД.
В CMS — вкладка "Схожі товари" на картці товару.

### Що показується якщо товар неактивний

Сторінка повертає 404.

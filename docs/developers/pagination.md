# Пагінація — Документація для розробника

Два підходи залежно від контексту. Обирай за таблицею:

| Критерій | `HasPagination` (Livewire SPA) | Eloquent `->paginate()` |
|----------|-------------------------------|------------------------|
| Перезавантаження сторінки | Ні (history.pushState) | Так |
| Livewire компонент | Потрібен | Не потрібен |
| "Показати ще" | ✅ через API endpoint | ⚠️ потребує API endpoint |
| Підходить для | Каталог, пошук, будь-який live-список | Blog, новини, прості сторінки |

---

## 1. Livewire SPA пагінація — `HasPagination` trait

Для Livewire компонентів, де зміна сторінки не перезавантажує браузер.

### Підключення

```php
use App\Livewire\Concerns\HasPagination;
use Livewire\Component;

class MyList extends Component
{
    use HasPagination;

    public function mount(Category $category): void
    {
        $this->bootPagination($category->getUrl()); // ← обов'язково в mount()
        // $this->perPage = 12; // за замовчуванням 24
    }

    #[Computed]
    public function items(): LengthAwarePaginator
    {
        $result = ...; // TypeSense або Eloquent

        return $this->makePaginator(
            items: $result['products'],
            total: $result['total'],
        );
    }
}
```

### Що робить `bootPagination($canonicalUrl)`

- `paginatorPath` — шлях з `$model->getUrl()`, встановлюється **один раз** в `mount()`
- `paginatorQuery` — GET-параметри початкового запиту (не AJAX-тіло Livewire)
- Без цього: при AJAX-оновленні `request()->path()` = `/livewire-xxx/update` → пагінатор генерує хибні посилання

### Що дає `makePaginator()`

Повертає `LengthAwarePaginator` з правильним `withPath()` і `appends()`.

### Шаблон пагінатора

```blade
{{ $paginator->links('catalog.pagination') }}
```

Шаблон: `resources/views/catalog/pagination.blade.php`  
Містить: "Показати ще" кнопку + нумерація. Посилання на сторінку 1 — без `?page=1`.

### Як JS перехоплює кліки

`pagination.js` (глобальний слухач):
```
клік [data-js-paginator] a
  → e.preventDefault()
  → history.pushState(url)
  → Livewire.dispatch('page-changed', { page })
    → #[On('page-changed')] setPage() ← з HasPagination trait
```

**Обгортка пагінатора** в Blade компонента:
```blade
{{-- data-js-paginator потрібен для pagination.js --}}
{{-- вже є всередині catalog.pagination view --}}
{{ $paginator->links('catalog.pagination') }}
```

### "Показати ще" (load-more)

Потребує **API endpoint**, що повертає HTML фрагменти сторінки.

```blade
{{-- data-api-url на гриді — читається load-more.js --}}
<div data-js-product-grid
     data-api-url="{{ route('api.v1.catalog.products-html', $category) }}">
```

`load-more.js` робить `fetch(apiUrl + ?page=N)`, парсить HTML і append-ить картки.  
Приклад API: `Api\v1\CatalogController::getProductsHtml()`.

**Для інших моделей** — потрібно додати аналогічний API endpoint і передати `data-api-url`.

---

## 2. Стандартна Eloquent пагінація

Для звичайних Blade-сторінок без Livewire.

### Підключення

```php
// Controller
public function index(): View
{
    $posts = Post::query()
        ->orderByDesc('created_at')
        ->paginate(12);

    return view('blog.index', compact('posts'));
}
```

```blade
{{-- Blade view --}}
@foreach($posts as $post)
    @include('partials.blog.post-card', compact('post'))
@endforeach

{{ $posts->links('catalog.pagination') }}
{{-- або стандартний Bootstrap/Tailwind view: $posts->links() --}}
```

### "Показати ще" для Eloquent

Потребує окремого API endpoint:

```php
// routes/api.php
Route::get('/blog/posts-html', [BlogController::class, 'postsHtml'])
     ->name('api.blog.posts-html');
```

```blade
<div data-js-product-grid
     data-api-url="{{ route('api.blog.posts-html') }}">
    @foreach($posts as $post) ... @endforeach
</div>

{{ $posts->links('catalog.pagination') }}
```

`load-more.js` вже підключений глобально і читає `data-api-url` автоматично.

---

## Шаблон пагінатора: `catalog.pagination`

Файл: `resources/views/catalog/pagination.blade.php`

Отримує від Laravel:
- `$paginator` — об'єкт `LengthAwarePaginator`
- `$elements` — масив сторінок і URL

Особливості:
- Посилання на сторінку 1 → `$paginator->path()` (без `?page=1`)
- "Попередня" (←) з сторінки 2 → теж `$paginator->path()`
- Кнопка "Показати ще" — лише якщо є наступні сторінки
- Обгорнутий в `<div data-js-paginator>` для `pagination.js`

---

## Підключення нової сторінки з пагінацією — чекліст

```
[ ] Controller: ->paginate(N) або TypeSense + makePaginator()
[ ] Livewire компонент: use HasPagination; + bootPagination($model->getUrl())
[ ] Blade: {{ $paginator->links('catalog.pagination') }}
[ ] "Показати ще": API endpoint + data-api-url на гриді
[ ] JS вже підключений глобально (pagination.js, load-more.js)
```

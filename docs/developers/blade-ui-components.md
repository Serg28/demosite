# Blade UI-компоненти

Документація для UI-компонентів у `resources/views/components/`.

---

## `<x-phone-input>` — Поле телефону

### Файли
- `resources/views/components/phone-input.blade.php`
- `resources/js/components/phone-input.js`

### Пропси

| Prop | Тип | Дефолт | Опис |
|------|-----|--------|------|
| `label` | string | `''` | Підпис поля |
| `required` | bool | `false` | Позначка обов'язковості |
| `error` | string\|null | `null` | Текст помилки ззовні (override $errors) |

### Використання

**У Livewire-компоненті (автоматичне отримання помилок):**
```blade
<x-phone-input
    wire:model="form.phone"
    :label="__t('Телефон')"
    :required="true"
/>
```

**У звичайному Blade (помилка ззовні):**
```blade
<x-phone-input
    wire:model="phone"
    :label="__t('Телефон')"
    :error="$errors->first('phone')"
/>
```

**З підказкою про помилку з Action/Service:**
```blade
<x-phone-input
    name="phone"
    :label="__t('Телефон')"
    :error="$customError"
/>
```

### Поведінка
- Маска телефону (IMask) — тільки цифри, формат `+38 (0XX) XXX-XX-XX`
- Wire:model працює через `x-model` (Alpine entangle-подібно)
- `$error` prop перевизначає `$errors->first($wireModel)`

---

## `<x-email-input>` — Поле email

### Файли
- `resources/views/components/email-input.blade.php`
- `resources/js/components/email-input.js`

### Пропси

| Prop | Тип | Дефолт | Опис |
|------|-----|--------|------|
| `label` | string | `''` | Підпис поля |
| `required` | bool | `false` | Позначка обов'язковості |
| `placeholder` | string | `'example@mail.com'` | Placeholder |
| `error` | string\|null | `null` | Текст помилки ззовні |

### Використання

```blade
<x-email-input
    wire:model="form.email"
    :label="__t('Email')"
    :required="true"
/>
```

### Поведінка
- **Фільтр кирилиці**: при введенні нелатинських символів — миттєво видаляє
- **Debounce валідації**: 500ms після зупинки введення → базова валідація формату
- `$error` prop перевизначає `$errors->first($wireModel)` — для використання поза Livewire

---

## `<x-breadcrumbs>` — Хлібні крихти

### Файли
- `resources/views/components/breadcrumbs.blade.php`

### Пропси

| Prop | Тип | Дефолт | Опис |
|------|-----|--------|------|
| `items` | `list<BreadcrumbItem>` | `[]` | Масив крихт |

### Використання

```blade
<x-breadcrumbs :items="$breadcrumbs ?? []" />
```

Завжди `$breadcrumbs` передається з контролера через `BreadcrumbsService`. Дивись `docs/developers/breadcrumbs.md`.

---

## `<x-buy-button>` — Кнопка "Купити"

### Файли
- `resources/views/components/buy-button.blade.php`

### Пропси

| Prop | Тип | Опис |
|------|-----|------|
| `product` | `Product` | Модель товару |

### Використання

```blade
<x-buy-button :product="$product" />
```

---

## Загальне правило для портабельних компонентів

Щоб компонент можна було використовувати і в Livewire, і в звичайному Blade:

```blade
@props(['label' => '', 'required' => false, 'error' => null])
@php
    $wireModel  = $attributes->get('wire:model', '');
    $fieldError = $error ?? ($wireModel ? $errors->first($wireModel) : '');
@endphp

<div>
    @if($label)
        <label>{{ $label }}@if($required) <span>*</span> @endif</label>
    @endif

    <input {{ $attributes->except(['label', 'required', 'error']) }}
           class="{{ $fieldError ? 'border-red-400' : 'border-gray-200' }} ..." />

    @if($fieldError)
        <p class="text-red-500 text-xs mt-1">{{ $fieldError }}</p>
    @endif
</div>
```

Патерн: `$error` prop — приймає текст помилки явно. Якщо не вказано, компонент автоматично читає `$errors->first($wireModel)`.

---

## `data-js-like` / `data-js-compare` — Like & Compare кнопки

### Файли
- `resources/js/base/like.js`
- `resources/js/base/compare.js`

### Патерн розмітки

```blade
{{-- Like --}}
<button type="button"
        data-js-like
        data-id="{{ $product->id }}"
        data-active="{{ Auth::check() && Auth::user()->hasFavorite($product->id) ? 'true' : 'false' }}">
    <svg>...</svg>
</button>

{{-- Compare --}}
<button type="button"
        data-js-compare
        data-id="{{ $product->id }}"
        data-active="{{ in_array($product->id, session('compare_ids', [])) ? 'true' : 'false' }}">
    <svg>...</svg>
</button>
```

### Правила

1. **data-attribute, не CSS-клас** — JS знаходить кнопки через `[data-js-like]` / `[data-js-compare]`
2. `data-id` — обов'язково, ідентифікатор товару
3. `data-active="true/false"` — ініціальний стан (true = вже в списку)
4. `type="button"` — обов'язково, щоб не сабмітити форму

### MutationObserver

`like.js` і `compare.js` спостерігають за DOM-мутаціями — нові кнопки, додані Livewire або JS, автоматично отримують обробники. Не треба викликати `init()` вручну.

# Modal Manager + Toast Notifications

> **Статус:** Livewire 4 native (замінює `wire-elements/modal`)
> **TODO:** Винести `ModalManager` + `IsModalForm` у пакет `linecore/modal`

---

## Навіщо, а не wire-elements/modal

`wire-elements/modal v2` несумісний з Livewire 4 (потребує `^3`). Наш `ModalManager` реалізує
**ідентичний API** — зміна мінімальна при переносі коду між проектами.

---

## ModalManager

### Розміщення в layout

`resources/views/components/layouts/shop.blade.php`:
```blade
<livewire:components.modal-manager />
```

### Виклик з Blade (через `[data-js-modal]`)

```blade
<button
    data-js-modal
    data-component="forms.recall"
    data-subject="Зворотній дзвінок"
    data-product-id="{{ $product->id }}"
>
    Дзвінок
</button>
```

Всі `data-*` (крім `data-component`) передаються як `arguments` у компонент.

### Виклик з PHP (Livewire)

```php
$this->dispatch('openModal', component: 'forms.recall', arguments: ['subject' => 'Дзвінок']);
$this->dispatch('closeModal');

// Закрити і диспетчеризувати події
$this->dispatch('closeModalWithEvents', events: [
    ['name' => 'cart-updated'],
    ['name' => 'product-added', 'params' => ['id' => $productId]],
]);
```

### Виклик з JS

```js
Livewire.dispatch('openModal', { component: 'forms.recall', arguments: { subject: 'Дзвінок' } });
Livewire.dispatch('closeModal');
```

---

## Створення модального компонента

```bash
php artisan make:livewire Forms/RecallForm
```

```php
<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\IsModalForm;
use Livewire\Component;

class RecallForm extends Component
{
    use IsModalForm;

    public string $subject = '';
    public string $phone   = '';

    // Перевизначає max-width (Tailwind клас)
    public static function modalMaxWidth(): string
    {
        return 'max-w-md';
    }

    public function submit(): void
    {
        $this->validate(['phone' => 'required']);
        // ... логіка

        $this->closeModal();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.forms.recall-form');
    }
}
```

```blade
{{-- resources/views/livewire/forms/recall-form.blade.php --}}
<div class="p-6">
    <h2 class="text-lg font-semibold mb-4">{{ $subject }}</h2>
    <form wire:submit="submit">
        <input wire:model="phone" type="tel" ...>
        <button type="submit">Відправити</button>
    </form>
</div>
```

> **ВАЖЛИВО:** view компонента повинен мати єдиний кореневий `<div>`.

### Міграція з wire-elements/modal

| wire-elements/modal | demo.loc |
|---|---|
| `extends ModalComponent` | `use IsModalForm` |
| `$this->closeModal()` | `$this->closeModal()` ✅ |
| `$this->closeModalWithEvents([...])` | `$this->closeModalWithEvents([...])` ✅ |
| `static modalMaxWidth()` | `static modalMaxWidth()` ✅ |
| `<livewire:modal />` | `<livewire:components.modal-manager />` |

---

## Toast Notifications

### Розміщення в layout

```blade
<x-toast />
```

### Виклик з PHP (Livewire + трейт HasNotifications)

```php
use App\Livewire\Concerns\HasNotifications;

class CartSidebar extends Component
{
    use HasNotifications;

    public function add(int $productId): void
    {
        // ...
        $this->notifySuccess('Товар додано в кошик');
        $this->notifyError('Товару немає в наявності');
        $this->notifyInfo('Оновлено ціну');
        $this->notifyWarning('Залишилось 2 шт.');
        $this->notify('Повідомлення', 'Заголовок', 'success');
    }
}
```

### Виклик з JS

```js
window.notify('success', 'Товар додано');
window.notify('error', 'Помилка', 'Заголовок');
Livewire.dispatch('notify', { type: 'info', message: 'Оновлено', title: '' });
```

### Типи: `success` | `error` | `info` | `warning`

---

## Lazy image loading

Атрибутний підхід — жодних кастомних елементів:

```blade
<img
    loading="lazy"
    data-src="{{ $product->image_url }}"
    src="/images/placeholder.svg"
    alt="{{ $product->name }}"
>
```

`lazy-loading-img.js` автоматично обробляє зміни DOM (MutationObserver) і Livewire morph.

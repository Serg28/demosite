# Livewire 4 Form Classes

## Головне правило

**Вся логіка форми — у Form-класі. Компонент тільки делегує.**

`validate()`, `save()`, `update()`, `fill()`, `reset()` — завжди у Form, ніколи в компоненті.

---

## Коли створювати Form клас

- Форма з 3+ полями
- Логіка, яка може бути потрібна в кількох компонентах
- Зберігання/оновлення моделей

Для 1-2 простих полів без збереження (`showForm = true/false`) — Form-клас не обов'язковий.

---

## Структура Form-класу

```php
// app/Livewire/Forms/{Domain}/{Name}Form.php
namespace App\Livewire\Forms\Profile;

use App\Models\User;
use App\Models\UserContact;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RecipientForm extends Form
{
    // 1. Властивості з #[Validate] — валідація на рівні поля
    #[Validate('required|min:2|max:60')]
    public string $first_name = '';

    #[Validate('required|min:10|max:20')]
    public string $phone = '';

    #[Validate('nullable|email|max:100')]
    public string $email = '';

    // 2. messages() — локалізовані повідомлення через __t()
    protected function messages(): array
    {
        return [
            'first_name.required' => __t("Вкажіть ім'я"),
            'phone.required'      => __t('Вкажіть номер телефону'),
            'email.email'         => __t('Невірний формат email'),
        ];
    }

    // 3. fill() — заповнення з моделі (для редагування)
    public function fill(UserContact $contact): void
    {
        $this->first_name = $contact->first_name ?? '';
        $this->phone      = $contact->phone       ?? '';
        $this->email      = $contact->email        ?? '';
        $this->is_primary = (bool) $contact->is_primary;
    }

    // 4. save() — створення нового запису; validate() всередині
    public function save(User $user): UserContact
    {
        $this->validate();  // ← ЗАВЖДИ тут, НЕ в компоненті

        return $user->contacts()->create([
            'type'       => 'recipient',
            'first_name' => $this->first_name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
        ]);
    }

    // 5. update() — оновлення існуючого запису; validate() всередині
    public function update(UserContact $contact): void
    {
        $this->validate();  // ← ЗАВЖДИ тут

        $contact->update([
            'first_name' => $this->first_name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
        ]);
    }

    // 6. reset() — скидання стану форми; викликати parent::reset()
    public function reset(...$properties): void
    {
        $this->first_name = '';
        $this->phone      = '';
        $this->email      = '';
        parent::reset(...$properties);
    }
}
```

---

## Структура компонента-менеджера

Компонент відповідає **тільки за UI-стан** (showForm, editingId) та делегує операції у Form.

```php
// app/Livewire/{Domain}/Manager.php
class RecipientManager extends Component
{
    public RecipientForm $form;          // Form-об'єкт
    public bool $showForm  = false;      // UI-стан
    public ?int $editingId = null;       // null = новий запис, int = редагування

    // mount() або boot() — ініціалізація, якщо потрібна

    public function edit(int $id): void
    {
        $contact = UserContact::where('user_id', Auth::id())->findOrFail($id);
        $this->form->fill($contact);     // ← делегація в Form
        $this->editingId = $id;
        $this->showForm  = true;
    }

    public function save(): void         // Єдиний метод — save або update
    {
        if ($this->editingId) {
            $this->update();
        } else {
            $this->add();
        }
    }

    public function add(): void
    {
        $this->form->save(Auth::user()); // ← делегація в Form (validate всередині)
        $this->form->reset();
        $this->showForm = false;
        $this->dispatch('recipient-added');
    }

    public function update(): void
    {
        $contact = UserContact::where('user_id', Auth::id())->findOrFail($this->editingId);
        $this->form->update($contact);   // ← делегація в Form (validate всередині)
        $this->form->reset();
        $this->editingId = null;
        $this->showForm  = false;
        $this->dispatch('recipient-updated');
    }

    public function delete(int $id): void
    {
        UserContact::where('user_id', Auth::id())->find($id)?->delete();
    }

    public function cancelForm(): void
    {
        $this->form->reset();
        $this->editingId = null;
        $this->showForm  = false;
    }
}
```

---

## Blade-шаблон компонента

```blade
{{-- Кнопка "Редагувати" в списку --}}
<button wire:click="edit({{ $contact->id }})" type="button">Редагувати</button>

{{-- Форма — динамічний заголовок залежно від режиму --}}
@if($showForm)
    <h3>{{ $editingId ? __t('Редагувати') : __t('Новий запис') }}</h3>

    <form wire:submit.prevent="save">
        <input wire:model="form.first_name" type="text">
        @error('form.first_name') <p>{{ $message }}</p> @enderror

        <button type="submit">
            {{ $editingId ? __t('Зберегти') : __t('Додати') }}
        </button>
        <button type="button" wire:click="cancelForm">{{ __t('Скасувати') }}</button>
    </form>
@endif
```

---

## Реальні приклади в проекті

| Form клас | Компонент | Використання |
|-----------|-----------|--------------|
| `RecipientForm` | `Recipient\Manager` | CRUD отримувачів у профілі |
| `AddressForm` | `Address\Manager` | CRUD адрес + location data |
| `EditDataForm` | `Profile\EditData` | Редагування даних профілю |

---

## Анти-паттерни (❌)

```php
// ❌ Валідація в компоненті
public function save(): void
{
    $this->validate([...]);  // НЕ ТАК
    ...
}

// ❌ SQL в компоненті
public function update(): void
{
    $contact->update([...]);  // НЕ ТАК — має бути в Form::update()
}

// ❌ Form без fill()
// Якщо форму треба редагувати — fill() обов'язковий

// ❌ Form без reset() override
// Якщо є bool-поля або поля зі значеннями != '' — reset() треба перевизначити
```

---

## Атрибути Form-властивостей

```php
#[Validate('required')]
public string $name = '';         // Валідується

#[Locked]
public int $userId = 0;           // НЕ може бути змінений з фронтенду (безпека)

public bool $is_primary = false;  // Простий стан, валідація в rules() або не потрібна
```

---

## #[Computed] vs public bool для Alpine

**Критично**: `#[Computed]` методи не потрапляють у Livewire snapshot і **недоступні** через `$wire.prop` в Alpine.js.

```php
// ❌ НЕ використовувати для Alpine
#[Computed]
public function isFormValid(): bool { ... }

// ✅ Використовувати public bool — оновлювати в updated() і методах
public bool $isFormValid = false;

public function updated(): void
{
    $this->isFormValid = $this->validateForm();
}
```

Правило: `#[Computed]` — тільки для `$this->prop` у PHP/Blade. Для `$wire.prop` в Alpine — `public bool/string/int`.

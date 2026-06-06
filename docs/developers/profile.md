# Особистий кабінет (Profile)

## Архітектура

```
ProfileController           → маршрутизація, view + breadcrumbs
profile.{section}           → Blade-шаблони із sidebar
livewire:{section}.manager  → Livewire CRUD-компоненти
Livewire\Forms\Profile\*    → Form-класи з логікою валідації/збереження
UserContact                 → Єдина модель для адрес і отримувачів (type = address|recipient)
```

---

## Маршрути (`routes/web.php`)

```php
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',           [ProfileController::class, 'index'])         ->name('index');
    Route::get('/orders',     [ProfileController::class, 'orders'])        ->name('orders');
    Route::get('/order/{id}', [ProfileController::class, 'ordersDetails']) ->name('order');
    Route::get('/addresses',  [ProfileController::class, 'addresses'])     ->name('addresses');
    Route::get('/recipients', [ProfileController::class, 'recipients'])    ->name('recipients');
    Route::get('/discounts',  [ProfileController::class, 'discounts'])     ->name('discounts');
    Route::get('/favorites',  [ProfileController::class, 'favorites'])     ->name('favorites');
    Route::get('/compare',    [ProfileController::class, 'compare'])       ->name('compare');
    Route::get('/security',   [ProfileController::class, 'security'])      ->name('security');
    Route::post('/logout',    [ProfileController::class, 'logout'])        ->name('logout');
});
```

---

## Структура файлів

### Контролер
`app/Http/Controllers/Shop/ProfileController.php`

Кожен метод приймає `BreadcrumbsService $breadcrumbs` і передає у view:
```php
public function addresses(BreadcrumbsService $breadcrumbs): View
{
    return view('profile.addresses', [
        'breadcrumbs' => $breadcrumbs->forProfile(__t('Адреси доставки')),
        'action'      => 'addresses',
    ]);
}
```

### Blade-шаблони
`resources/views/profile/`
```
index.blade.php         → Персональні дані
orders.blade.php        → Список замовлень
order.blade.php         → Деталі замовлення
addresses.blade.php     → Адреси доставки
recipients.blade.php    → Отримувачі
discounts.blade.php     → Знижки та бонуси
favorites.blade.php     → Список бажань
compare.blade.php       → Порівняння
security.blade.php      → Безпека
partials/sidebar.blade.php → Sidebar навігація
```

### Livewire-компоненти
`app/Livewire/Profile/`
```
Address/Manager.php     → CRUD адрес
Recipient/Manager.php   → CRUD отримувачів
Discount/Lists.php      → Список дисконтних карток
Order/Lists.php         → Список замовлень
Order/Details.php       → Деталі замовлення
EditData.php            → Редагування персональних даних
```

### Form-класи
`app/Livewire/Forms/Profile/`
```
AddressForm.php         → Форма адреси (label, phone, is_primary + locationData)
RecipientForm.php       → Форма отримувача (first_name, last_name, phone, email, is_primary)
EditDataForm.php        → Форма даних профілю (last_name, first_name, patronymic, phone, email)
```

---

## Модель UserContact

Єдина таблиця `user_contacts` для двох типів контактів:

| Поле | Тип | Опис |
|------|-----|------|
| `type` | enum | `address` або `recipient` |
| `user_id` | int | Власник |
| `label` | string\|null | Підпис (лише для адрес) |
| `first_name` | string\|null | Ім'я (лише для отримувачів) |
| `last_name` | string\|null | Прізвище (лише для отримувачів) |
| `phone` | string\|null | Телефон |
| `email` | string\|null | Email (лише для отримувачів) |
| `is_primary` | bool | Основний контакт |
| `info` | json | Довільні дані (для адрес — місто, доставка, відділення) |

### Scope-методи на User
```php
$user->addresses()    // type = address
$user->recipients()   // type = recipient
```

---

## Sidebar

`profile.partials.sidebar` — включається у кожен шаблон профілю:

```blade
@include('profile.partials.sidebar', ['action' => 'addresses'])
```

`$action` — поточна секція, використовується для підсвічення активного пункту меню.

---

## CRUD-паттерн (Addresses / Recipients)

Обидва компоненти (`Address\Manager`, `Recipient\Manager`) дотримуються однакового паттерну:

1. `showForm` / `editingId` — UI-стан у компоненті
2. Кнопка "Редагувати" → `wire:click="edit({{ $contact->id }})"`
3. `edit()` → `$this->form->fill($contact)` + `$editingId = $id`
4. Submit → `wire:submit.prevent="save"` → `save()` → `add()` або `update()`
5. `add()` / `update()` → делегація в `$this->form->save()` / `$this->form->update()`
6. Після збереження → `$this->form->reset()`, `$editingId = null`, `$showForm = false`
7. Dispatch: `address-added` / `address-updated` / `recipient-added` / `recipient-updated`

Детальніше про Form-паттерн: `docs/developers/livewire-forms.md`

---

## Адреси — особливості

`Address\Manager` містить додаткові поля для вибору місцезнаходження (не у Form):
- `cityId`, `cityTitle` — вибір міста (autocomplete via Alpine + fetch)
- `deliveryId`, `deliverySlug` — вибір способу доставки
- `warehouseId`, `warehouseTitle` — вибір відділення (autocomplete)
- `street`, `house`, `apartment` — адресна доставка

`AddressForm` зберігає тільки `label`, `phone`, `is_primary`. Геолокація передається окремим масивом `$locationData` у `save()` / `update()`.

---

## Breadcrumbs

Всі методи контролера викликають `BreadcrumbsService::forProfile(string $pageTitle)`:

```php
// Головна кабінету — без заголовка
$breadcrumbs->forProfile()
// → [Головна] / [Кабінет]

// Підсторінка
$breadcrumbs->forProfile(__t('Адреси доставки'))
// → [Головна] / [Кабінет] / [Адреси доставки]

// Деталі замовлення — використовуємо simple() для вкладеності
$breadcrumbs->simple(
    __t('Замовлення #') . $orderId,
    [route('profile.index') => __t('Кабінет'), route('profile.orders') => __t('Мої замовлення')]
)
// → [Головна] / [Кабінет] / [Мої замовлення] / [Замовлення #123]
```

---

## Тести

`tests/Feature/Profile/`
```
AddressManagerTest.php      → CRUD адрес
RecipientManagerTest.php    → CRUD отримувачів
EditDataTest.php            → Редагування даних
DiscountListsTest.php       → Список знижок
OrderListsTest.php          → Список замовлень
OrderActionsTest.php        → Дії з замовленнями (скасувати, оплатити, повторити)
ProfileControllerTest.php   → Маршрути та breadcrumbs
SecurityTest.php            → Зміна пароля
CumulativeDiscountTest.php  → Накопичувальні знижки
```

Запуск: `php artisan test tests/Feature/Profile/ --compact`

---
paths:
  - "app/Livewire/**/*.php"
  - "resources/views/livewire/**/*.blade.php"
---

# Livewire: авторизація та безпека (Fortify)

Auth — стандартний Laravel `Auth`-фасад (Laravel Fortify). Поточний юзер: `Auth::user()`
(повертає `App\Models\User` або `null`), не Sentinel/`app('user')` — це інший рушій, ніж у
Shambala/linecore-demo. `app/Policies` — перевір актуальний стан перед новим кодом.

## Критично: route-middleware не захищає екшени

`routes/web.php` обгортає `/livewire/update` через `Livewire::setUpdateRoute()` з
`block.bot_request`/`validate.livewire.method` — це захист від ботів і method-injection,
**не авторизація**. `mount()`-перевірка захищає лише перший рендер — **кожен public-метод сам
перевіряє авторизацію** через `Auth::user()`, не покладаючись на middleware чи на властивість,
кешовану в `mount()`.

```php
public function disconnectProvider(int $providerId): void
{
    $user = Auth::user();
    if (! $user) {
        abort(403);
    }

    $user->providers()->findOrFail($providerId)->delete(); // ownership через relation, не Model::find($id)
}
```

## Заборонено: авторизація через mount()-властивість

```php
// ❌ public User $user; (з mount()) → if (!$this->user?->id) { ... }
```

Livewire хайдратить Eloquent-властивість по `id` зі snapshot (`ModelSynth`), **без повторної
перевірки сесії**. Ідентичність з `mount()` могла протухнути (logout в іншій вкладці, bfcache,
спільний браузер) — hydrate все одно поверне стару модель. Кожен action викликає `Auth::user()`
заново; `$this->user`, встановлений у `mount()`, — тільки для читання/`render()`, не для авторизації.

## Правила

1. Кожен public-метод і `mount()` — перевіряти `Auth::check()`/`Auth::user()` явно, не
   покладатись на факт виклику з авторизованої сторінки.
2. Ownership — через relation юзера (`$user->orders()->findOrFail($id)`), ніколи
   `Model::find($id)` за id з `wire:click`. `ModelNotFoundException` на чужому id — очікувано,
   ловити й логувати. Аргументи типізувати (`int $id`).
3. `#[Locked]` — лише для public-**властивостей**, не для аргументів методів (там — правило 2).
   Блокує зміну з фронта, не з бекенду.
4. Нічого секретного в public-властивостях: snapshot підписаний (tamper-proof), але НЕ зашифрований —
   скаляри/масиви видно в HTML. Eloquent-модель безпечніша (лише `class+id`), але не звільняє від правила 1.
5. `protected`/`private` для helper-методів — будь-який `public` метод викликається з фронта.
6. Policy (`$this->authorize()`/`Gate::`) і `Livewire\Form` (`#[Validate]`) — стандартний шлях
   для нових компонентів (Fortify-стек це підтримує нативно, на відміну від Sentinel-проєктів).

## Livewire 4: перевір перед використанням

Цей файл перенесено з проєкту на Livewire 3 (Sentinel). Базові факти (route-middleware не
захищає екшени, mount()-властивість не для auth) — властивості архітектури Livewire, які не
залежать від версії. Але конкретні деталі API/конфігу (Islands, `wire:model` за замовчуванням,
namespace компонентів) — звір з `livewire-development-4` скілом і актуальним
`config/livewire.php`, не переноси версійну специфіку зі старого проєкту мовчки.

## Чекліст

- [ ] Кожен action сам перевіряє `Auth::user()`, не покладається на mount()-властивість
- [ ] Ownership — через relation юзера; аргументи типізовані
- [ ] Жодних секретів у public-властивостях
- [ ] Helper-методи — `protected`/`private`

# Frontend Auth — перехід на Sentinel

**Пріоритет:** Середній  
**Залежності:** Виконати до реалізації особистого кабінету (Profile, Orders, Wishlist)

---

## Проблема

Demo.loc зараз використовує два несумісних auth-шари:

| Шар | Зараз | Має бути |
|-----|-------|----------|
| CMS admin | Sentinel ✓ | Sentinel |
| Frontend клієнти | Fortify (закоментовано, не працює) | Sentinel |

В production-проекті (linecore-demo) **Sentinel використовується скрізь** — і для CMS, і для frontend-клієнтів. Demo.loc має відповідати цьому патерну.

Без цього:
- Sentinel-групи/ролі недоступні для frontend-юзерів
- `app('user')` повертає Sentinel-юзера (з групами), але в demo.loc цей singleton не налаштований
- `RegisterUser` в checkout використовує `User::firstOrCreate()` — Laravel auth, не Sentinel

---

## Що потрібно зробити

### 1. App\Models\User — змінити базовий клас

```php
// Зараз:
class User extends Authenticatable { ... }

// Має бути:
use Linecore\Cms\User as SentinelUser;

class User extends SentinelUser { ... }
```

### 2. AppServiceProvider — `app('user')` singleton

За патерном linecore-demo (`app/Providers/AppServiceProvider.php`):

```php
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Users\UserInterface;

App::singleton('user', static function (): UserInterface|bool|null {
    try {
        return Sentinel::check();
    } catch (NotActivatedException $e) {
        return null;
    }
});
```

> `Sentinel::check()` повертає юзера (з групами/ролями) або `false`. Singleton живе в межах одного запиту — безпечно.

### 3. Замінити auth-перевірки

| Зараз | Замінити на |
|-------|------------|
| `auth()->check()` | `Sentinel::check()` або `app('user') !== null` |
| `auth()->user()` | `app('user')` або `Sentinel::getUser()` |
| `auth()->id()` | `app('user')->id` |
| `middleware('auth')` | `middleware('sentinel.frontend')` (аліас на `AuthenticateFrontend`) |

Виняток: `auth()->check()` в checkout-коді (RegisterUser тощо) — там можна залишити через custom Sentinel guard або адаптер.

### 4. RegisterUser action — перевести на Sentinel

```php
// Зараз: User::firstOrCreate(...)
// Має бути:
$user = Sentinel::findByCredentials(['email' => $email])
    ?? Sentinel::registerAndActivate([
        'email'      => $email,
        'first_name' => $order->first_name,
        'last_name'  => $order->last_name,
        'password'   => Str::random(16),
    ]);
```

### 5. Прибрати Fortify з frontend

- Видалити або залишити закоментованими auth-маршрути в `routes/auth.php`
- Прибрати `Features::resetPasswords()` з `config/fortify.php` (додано тимчасово)
- Замінити email-reset через Sentinel Reminders або власний механізм

> Fortify можна залишити тільки для 2FA (`Features::twoFactorAuthentication()`), якщо потрібно.

---

## Способи авторизації (обов'язково)

Архітектура повинна дозволяти легке підключення методів без зміни ядра.

### Базові (Phase X)
- [ ] **Email + password** — `Sentinel::authenticate()`
- [ ] **Forgot password** — Sentinel Reminders або кастомний flow з листом

### Розширені (Phase X+1)
- [ ] **OTP (SMS/email)** — вже є `data-component="auth.by-otp.login"` у формі. Зробити Livewire-компонент, незалежний від основного auth-метода
- [ ] **Socialite** — Google, Facebook. Sentinel-user створюється/прив'язується по email
- [ ] **2FA (TOTP)** — через `TwoFactorAuthenticatable` або кастомно після Sentinel login

### Принцип розширення

```
SentinelAuthManager (фасад/сервіс)
  ├── EmailPasswordProvider
  ├── OtpProvider          ← підключається незалежно
  ├── SocialiteProvider    ← підключається незалежно
  └── TwoFactorProvider    ← другий крок після будь-якого provider
```

Кожен provider реалізує `AuthProviderInterface::attempt(array $credentials): ?User`.

---

## Групи/ролі на фронті

Після переходу на Sentinel `app('user')->groups` буде доступний у будь-якому місці:

```php
// Приклади майбутнього використання:
$user->inRole('wholesale')   // оптовий клієнт — своя ціна
$user->hasAccess(['comments.edit'])  // може редагувати коментарі
$user->groups->pluck('name')  // список груп для умов у шаблоні
```

---

## Файли для зміни

- `app/Models/User.php` — змінити базовий клас
- `app/Providers/AppServiceProvider.php` — додати singleton
- `app/Actions/Checkout/RegisterUser.php` — замінити на Sentinel::register
- `app/Mail/RegisteredFromCheckoutMail.php` — прибрати `route('password.reset')`
- `config/fortify.php` — прибрати `resetPasswords()`
- `routes/auth.php` — замінити на Sentinel-маршрути
- `bootstrap/app.php` або `Kernel.php` — зареєструвати `sentinel.frontend` middleware аліас
- Всі місця з `auth()->check()`, `auth()->user()`, `auth()->id()`

---

## Примітки

- `Sentinel::check()` != `auth()->check()` — не взаємозамінні без адаптера
- Якщо потрібна сумісність з `auth()` helper — налаштувати кастомний Sentinel guard (SentinelGuard implements Guard)
- Тести: перезаписати фабрики щоб створювали Sentinel-юзерів через `Sentinel::register()`

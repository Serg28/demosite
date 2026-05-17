<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:24px">
    <h2>{{ __t('Ваш акаунт створено') }}</h2>
    <p>{{ __t('Вітаємо, :name!', ['name' => $user->name]) }}</p>
    <p>{{ __t('Під час оформлення замовлення ми створили для вас особистий кабінет.') }}</p>
    <p>{{ __t('Для входу встановіть пароль за посиланням:') }}</p>
    <p style="margin:24px 0">
        <a href="{{ $resetUrl }}"
           style="background:#2563eb;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;display:inline-block">
            {{ __t('Встановити пароль') }}
        </a>
    </p>
    <p style="color:#888;font-size:13px">{{ __t('Посилання дійсне 60 хвилин.') }}</p>
</body>
</html>

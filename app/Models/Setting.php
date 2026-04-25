<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

class Setting extends \Vis\Builder\Setting
{
    protected static function boot()
    {
        parent::boot();

        static::updated(static function ($setting) {
            // Сохраняем нужные значения в файл .env
            $envMapping = self::getEnvMapping();

            if (array_key_exists($setting->slug, $envMapping)) {
                $value = $setting->type == 'checkbox' ? (bool)$setting->check : $setting->value;
                self::updateEnv($envMapping[$setting->slug], $value);

                Artisan::call('config:clear');
            }
        });
    }

    public function getGroups(): array
    {
        return [
            'general' => 'Общее',
            'social' => 'Социальные сети',
            'catalog_product'=>'Каталог и товары',
            'contacts' => 'Контакты',
            'contacts_front' => 'Контакты (шапка и подвал)',
            'messages' => 'Сервисные сообщения',
            'code' => 'Установка кода',
            'internet_marketing' => 'Интернет-маркетинг',
            'mail' => 'Почта',
            'currency' => 'Валюта',
            'order' => 'Уведомления про заказ',
            'integration' => 'Интеграции',
            'site_api' => 'Собственный API',
        ];
    }

    protected static function getEnvMapping()
    {
        return [
            'google-recaptcha-secret-key' => 'RECAPTCHA_SECRET_KEY',
            'google-recaptcha-site-key' => 'RECAPTCHA_SITE_KEY',
            'google-recaptcha-active' => 'RECAPTCHA_ACTIVE',
            'services.np.api_key' => 'NP_API_KEY',
            'monobank-monopay-token' => 'MONO_PAY_TOKEN',
            'wayforpay-test-mode' => 'WAYFORPAY_TEST',
            'wayforpay-account' => 'WAYFORPAY_ACCOUNT',
            'wayforpay-secret-key' => 'WAYFORPAY_SECRET_KEY',
            'google-client-id' => 'GOOGLE_CLIENT_ID',
            'google-client-secret' => 'GOOGLE_CLIENT_SECRET',
            'facebook-client-id' => 'FACEBOOK_CLIENT_ID',
            'facebook-client-secret' => 'FACEBOOK_CLIENT_SECRET'
        ];
    }

    public function scopeFilter(Builder $query, $group): Builder
    {
        return $query->whereGroup($group);
    }

    protected static function updateEnv($key, $value): void
    {
        $path = base_path('.env');
        $oldValue = env($key);

        // Преобразовываем булево значение в строку
        $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        $oldValue = is_bool($oldValue) ? ($oldValue ? 'true' : 'false') : $oldValue;

        file_put_contents($path, str_replace(
            "$key=$oldValue",
            "$key=$value",
            file_get_contents($path)
        ));

        // Обновляем текущее окружение
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

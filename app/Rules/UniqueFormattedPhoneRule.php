<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Helpers\PhoneNumberHelper;
use Illuminate\Support\Facades\Cache;

/**
 * Класс для проверки уникальности отформатированного номера телефона.
 *
 * Этот класс используется для проверки уникальности номера телефона
 * после его форматирования. Правило полезно в случаях, когда номера
 * телефонов могут быть введены в разных форматах, но их нужно привести
 * к единому виду перед проверкой уникальности.
 */
class UniqueFormattedPhoneRule implements Rule
{
    /**
     * Определяет, проходит ли атрибут проверки уникальности.
     *
     * @param  string  $attribute  Название атрибута.
     * @param  mixed  $value  Значение атрибута.
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Отформатировать номер телефона
        $formattedPhone = PhoneNumberHelper::clean($value);

        // Проверить уникальность отформатированного номера телефона.
        // Создать уникальный ключ для кэша
        $cacheKey = 'unique_phone_check_' . $formattedPhone;

        // Проверить наличие в кэше
        return Cache::remember($cacheKey, 300, static function () use ($formattedPhone) {
            // Проверить уникальность отформатированного номера телефона
            return !DB::table('users')->where('phone', $formattedPhone)->exists();
        });
    }

    /**
     * Получить сообщение об ошибке проверки.
     *
     * @return string
     */
    public function message()
    {
        return __t('Номер телефону вже використовується');
    }
}

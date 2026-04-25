<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    /**
     * Нормализует номер телефона к формату +380XXXXXXXXX.
     *
     * @param ?string $phoneNumber Номер телефона, который нужно нормализовать.
     *
     * @return ?string Нормализованный номер телефона в формате +380XXXXXXXXX.
     */
    public static function formatPhoneNumber(?string $phoneNumber): ?string
    {
        if ($phoneNumber) {
            // Удалить все нецифровые символы и символы "+"
            $phoneNumber = self::clean($phoneNumber);
            $phoneNumber = str_replace('+38', '', $phoneNumber);

            if (!empty($phoneNumber)) {
                // Если номер начинается с "8" и его длина равна 11 символам, считаем это частью префикса "+380"
                if ($phoneNumber[0] === '8' && strlen($phoneNumber) === 11) {
                    $phoneNumber = substr($phoneNumber, 1); // Удаляем первую "8"
                }

                // Удалить префикс +38 или 38, если он есть
                if (is_string($phoneNumber) && str_starts_with($phoneNumber, '380')) {
                    $phoneNumber = substr_replace($phoneNumber, '', 0, 2);
                }

                // Если номер не начинается с "0" и его длина равна 9 символам, добавить "0" в начало
                if ($phoneNumber[0] !== '0' && strlen($phoneNumber) === 9) {
                    $phoneNumber = '0'.$phoneNumber;
                }

                // Добавить префикс "+380"
                return '+38'.$phoneNumber;
            }
        }
        return null;
    }

    public static function clean(?string $phoneNumber): ?string
    {
        return preg_replace('/[^\d+]/', '', $phoneNumber);
    }
}

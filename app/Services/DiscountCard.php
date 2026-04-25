<?php

namespace App\Services;

use App\Helpers\PhoneNumberHelper;
use App\Models\DiscountCard as Discount;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

/**
 * Class DiscountCard
 *
 * Сервис для работы с дисконтными картами.
 *
 * @package App\Services
 */
class DiscountCard
{
    /*
     * Получение скидки для дисконтной карты по-умолчанию.
     *
     * @return int Скидка в процентах.
     */
    private function getDefaultDiscount(): int
    {
        return setting('skidka-po-diskontnoy-karte') ?: 0;
    }

    /*
     * Получение скидки на основе дисконтной карты пользователя.
     *
     * @return int Скидка в процентах. Возвращает 0, если пользователь не авторизован или не имеет дисконтной карты.
     */
    public function get(): int
    {
        // Получаем текущего пользователя
        //$user = Sentinel::check();
        //$user = app('user');

        try {
            $user = Sentinel::check();
        } catch (NotActivatedException $e) {
            $user = null;
        }

        // Если пользователь не авторизован, возвращаем 0
        // Неавторизованным юзерам нету доступа к дисконту. Если нужен, следующие строки можно закомментировать
        if (!$user) {
            return 0;
        }

        // Получаем номер телефона пользователя из сессии заказа или данных пользователя
        $form = session()->get('orderForm');
        $userPhone = $user->phone ?? '';
        $phone = $form ? ($form['phone'] ?? $userPhone) : $userPhone;

        // Очищаем номер телефона от специальных символов и добавляем "+" при необходимости
        $phoneClean = PhoneNumberHelper::formatPhoneNumber($phone);

        // Поиск дисконтной карты по номеру телефона
        if ($phoneClean && $card = Discount::where('phone', $phone)
                ->orWhere('phone', $phoneClean)
                ->orWhere('phone', '+' . $phoneClean)
                ->active()
                ->first()) {
            return $card->discount ?: $this->getDefaultDiscount();
        }

        // Если дисконтная карта не найдена, возвращаем значение по умолчанию
        return 0;
    }
}

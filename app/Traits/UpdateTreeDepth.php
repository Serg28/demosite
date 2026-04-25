<?php

namespace App\Traits;

/**
 * Trait UpdateTreeDepth
 *
 * Trait для  вычисления и обновления глубины узлов дерева.
 * Должен быть использован с моделями, использующими NestedSet.
 *
 * @package App\Traits
 */
trait UpdateTreeDepth
{
    /**
     * Метод для обновления глубины каждого узла дерева
     *
     * Извлечение всех узлов с назначенной глубиной, группировка их по глубине и обновление значений
     * Трактует любые исключения как сигнал сбоя, в таком случае будет возвращено false
     * Если ошибок не возникло, метод вернет true, обозначая успех операции
     *
     * @return bool статус операции обновления глубины узлов дерева
     */
    public static function fixDepthTree(): bool
    {
        try {
            // Получить все узлы с их глубиной
            $nodes = self::withDepth()->get();

            // Сгруппировать узлы по глубине и обновить их
            $nodes->groupBy('depth')->each(function ($group, $depth) {
                self::whereIn('id', $group->pluck('id'))->update(['depth' => $depth]);
            });

            // Если код дошел до этого места, значит исключений не случилось
            // Метод вернет true
            return true;

        } catch (\Exception $e) {
            // В случае какого-либо исключения метод вернет false
            return false;
        }
    }
}
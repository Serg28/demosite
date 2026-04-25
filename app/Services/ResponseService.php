<?php

/**
 * Сервис для формирования JSON-ответов.
 *
 * Этот класс предоставляет удобные методы для создания структурированных JSON-ответов
 * с использованием общего формата ответа для успешных операций и ошибок.
 */
namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Возвращает успешный JSON-ответ с сообщением и, при необходимости, кастомными данными.
     *
     * @param string $message Сообщение об успешном выполнении операции.
     * @param array $customData Кастомные данные, которые могут быть добавлены к ответу (по умолчанию пустой массив).
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(string $message, array $customData = []): JsonResponse
    {
        $response = ['status' => 'success', 'message' => $message];

        if (!empty($customData)) {
            $response['data'] = $customData;
        }

        return response()->json($response);
    }

    /**
     * Возвращает JSON-ответ об ошибке с сообщением и, при необходимости, кастомными данными.
     *
     * @param string $message Сообщение об ошибке.
     * @param array $customData Кастомные данные, которые могут быть добавлены к ответу (по умолчанию пустой массив).
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message, array $customData = []): JsonResponse
    {
        $response = ['status' => 'error', 'message' => $message];

        if (!empty($customData)) {
            $response['data'] = $customData;
        }

        return response()->json($response);
    }
}

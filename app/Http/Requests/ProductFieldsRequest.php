<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFieldsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //ID товара (сайта), по которому будет обновляться товар, обязательное
            'id' => ['integer'],
            //Цена товара. Если не передавать, остается неизменным
            'price' => ['numeric'],
            //Старая цена товара. Если не передавать, остается неизменным
            'price_old' => ['numeric', 'nullable'],
            // Статус товара - ID статуса товара (id сайта) из таблицы product_status (поле id)
            'product_status_id' => ['integer'],
            //Ожидаемая дата поступления товара. Если не передавать, остается неизменным
            'arrival_date' => ['string', 'nullable'],
            // Количество частей рассрочки Приват. Если не передавать, остается неизменным
            'privat_payparts_count' => ['integer'],
            // Количество частей рассрочки Моно. Если не передавать, остается неизменным
            'mono_payparts_count' => ['integer'],
            // Доступна ли рассрочка Моно. Если не передавать, остается неизменным (1 - да, 0 - нет)
            'has_mono_payparts' => ['integer'],
            // Доступна ли рассрочка Приват. Если не передавать, остается неизменным  (1 - да, 0 - нет)
            'has_privat_payparts' => ['integer']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

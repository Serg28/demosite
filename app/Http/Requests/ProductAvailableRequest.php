<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAvailableRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //ID товара (сайта), по которому будет обновляться товар, обязательное
            'id' => ['integer'],
            // Статус товара - ID статуса товара (id сайта) из таблицы product_status (поле id)
            'product_status_id' => ['integer'],
            //Ожидаемая дата поступления товара. Если не передавать, остается неизменным
            'arrival_date' => ['string', 'nullable'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPricesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //ID товара (сайта), по которому будет обновляться товар, обязательное
            'id' => ['integer'],
            //Цена товара. Если не передавать, остается неизменным
            'price' => ['numeric'],
            //Старая цена товара. Если не передавать, остается неизменным
            'price_old' => ['numeric', 'nullable']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

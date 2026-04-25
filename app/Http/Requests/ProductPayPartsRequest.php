<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPayPartsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //ID товара (сайта), по которому будет обновляться товар, обязательное
            'id' => ['integer'],
            // Количество частей рассрочки Приват. Если не передавать, остается неизменным
            'privat_payparts_count' => ['integer'],
            // Количество частей рассрочки Моно. Если не передавать, остается неизменным
            'mono_payparts_count' => ['integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

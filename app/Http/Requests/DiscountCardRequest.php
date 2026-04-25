<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|min:5|max:5',
            'barcode' => 'required|min:13|max:13',
            'name' => 'required',
            'second_name' => 'required',
            'patronymic_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'city' => 'required',
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : ''
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => implode('', [$this->code_1, $this->code_2, $this->code_3, $this->code_4, $this->code_5]),
            'barcode' => preg_replace('/[^A-Za-z0-9]/', '', $this->barcode),
            'name' => strip_tags($this->name),
            'second_name' => strip_tags($this->second_name),
            'patronymic_name' => strip_tags($this->patronymic_name),
            'phone' => strip_tags($this->phone),
            'email' => strip_tags($this->email),
            'city' => strip_tags($this->city),
        ]);
    }
}

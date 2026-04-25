<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAvaliability extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : ''
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => app('user')->id ?? null, //TODO: создать нового юзера или подключить имеющегося
        ]);
    }
}

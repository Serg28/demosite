<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Registration extends FormRequest
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
            //'email' => 'required|email',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', Rule::unique('users', 'phone')],
            'password' => 'required',
            're_password' => 'required',
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : ''
        ];
    }
}

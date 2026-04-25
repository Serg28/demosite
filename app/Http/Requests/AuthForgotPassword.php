<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthForgotPassword extends FormRequest
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
            'email' => 'required|email',
            //'g_recaptcha_response' => 'required|recaptcha',
        ];
    }
}

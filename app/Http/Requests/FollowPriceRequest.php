<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowPriceRequest extends FormRequest
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
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : '',
            'email' => 'required|email',
        ];
    }
}

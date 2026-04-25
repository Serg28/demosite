<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
            'name' => 'required',
            'phone' => 'required',
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : ''
        ];
    }
}

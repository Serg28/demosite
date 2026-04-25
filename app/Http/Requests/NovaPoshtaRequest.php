<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NovaPoshtaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'lastname' => 'required',
            'firstname' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'warehouse' => 'required',
            'np_places' => 'required',
            'np_price' => 'required',
            'np_comment' => 'required',
            'np_delivery_pay' => 'required',
            'np_places_array' => 'required',
            'orderid' => 'required',
            'np_order_id' => '',
            'np_after_payment_cost' => '',
            'np_weight_general_kg' => 'required',
            'np_volume_general_kg' => 'required',
            'np_volume_general_m3' => 'required',
        ];
    }
}

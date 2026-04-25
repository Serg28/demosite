<?php

namespace App\Http\Requests;

use App\Models\PromoCode;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;

class Order extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'city_id' => 'required',
            'delivery_id' => 'required',
            'pay_method_id' => 'required',
            'g_recaptcha_response' => 'required|recaptcha',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => app('user')->id ?? null,
            'cost' => $this->getCostWithoutSale(),
            'order_status_id' => 1,
            'sale' => $this->getSalePercent(),
            'cost_without_sale' => Cart::total(),
        ]);
    }

    private function getCostWithoutSale(): int
    {
        if ($this->getSalePercent()) {
            return Cart::total() - round(Cart::total() * ($this->getSalePercent() / 100));
        }

        return Cart::total();
    }

    private function getSalePercent(): int
    {
        if ($this->get('promo_code')) {
            $code = PromoCode::whereCode($this->get('promo_code'))->first();

            return $code->sale;
        }

        return 0;
    }
}

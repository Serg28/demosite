<?php

namespace App\Http\Requests;

use App\Models\LegalEntitiesRecipient;
use App\Services\Checkout;
use App\Services\UserService;
use Illuminate\Foundation\Http\FormRequest;

class Order extends FormRequest
{
    private $checkoutService;

    private $userService;

    public function __construct(
        Checkout $checkoutService,
        UserService $userService
    ) {
        $this->checkoutService = $checkoutService;
        $this->userService = $userService;
    }

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
            //'g_recaptcha_response' => 'required|recaptcha',
            'g_recaptcha_response' => config('recaptcha.active') ? 'required|recaptcha' : ''
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->checkoutService->calculateSales();
        $this->merge([
            'user_id' => $this->userService->getUserOrCreate($this),
            'cost' => $this->checkoutService->getTotal(),
            'order_status_id' => 1,
            'sale' => $this->checkoutService->getSalePercent(),

            //--
            'sale_promo' => $this->checkoutService->getPromoSale(),
            'sale_discount' => $this->checkoutService->getDiscountSale(),
            //'sale_promo_amount' => $this->checkoutService->getPromoSaleSum(),
            //'sale_discount_amount' => $this->checkoutService->getDiscountSaleSum(),
            //--

            'cost_without_sale' => $this->checkoutService->getSubtotal(),
            //'legal_entities_recipient_id' => $this->getRequisitesId(),
            //'num' => $this->genNumOrder()
        ]);
    }

    /*protected function getRequisitesId()
    {
        //return ($this->pay_method_id == 4) ? LegalEntitiesRecipient::where('is_default', 1)->value('id') : null;
        return LegalEntitiesRecipient::where('is_default', 1)->value('id');
    }*/
}

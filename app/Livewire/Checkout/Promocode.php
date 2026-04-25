<?php

namespace App\Livewire\Checkout;

use App\Livewire\Checkout\Checkout;
use App\Services\Checkout as CheckoutService;
use App\Services\ResponseService;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Promocode extends Component
{
    #[Rule('required|string')]
    public string $promocode;

    #[Locked]
    public string|null $message = '';

    private CheckoutService $checkoutService;

    public function boot(CheckoutService $checkoutService) {
        $this->checkoutService = $checkoutService;
        $this->promocode = $this->checkoutService->getPromoCode()->code ?? '';
    }

    //Применить промокод
    public function setPromocode()
    {
        $this->validate([
            'promocode' => 'required|string'
        ]);

        $code = $this->checkoutService->checkPromocode($this->promocode);
        $promoSaleSum = ($code) ? $this->checkoutService->calculatePromoSaleSum() : 0;

        if ($promoSaleSum > 0) {
            $this->message = __t('Промокод успішно застосовано');
            $response = ResponseService::success($this->message, ['sale' => $code->sale ?? 0]);
        } elseif ($code && !$promoSaleSum) {
            $this->message = __t('Введений вами промокод не застосовується до обраних товарів. Перегляньте умови використання промокоду');
            $response = ResponseService::error($this->message);
            $this->checkoutService->resetPromocode();
            $this->promocode = '';
        } elseif(!$this->promocode) {
            $this->message = __t('Введіть промокод');
            $response = ResponseService::error($this->message);
            $this->promocode = '';
        } else {
            $this->message = __t('Промокод не знайдено');
            $response = ResponseService::error($this->message);
            $this->promocode = '';
        }

        $this->dispatch('checkout-set-property', property: 'promocode', value: $this->promocode)->to(Checkout::class);
        return $this->dispatch('checkout-promocode-apply-result', code: $this->promocode, promosalesum: $promoSaleSum, response: $response)->to(Checkout::class);
    }

    public function resetPromocode()
    {
        $successMessage = __t('Промокод успішно скасовано');
        $errorMessage = __t('Промокоду не вдалося скасувати. Спробуйте трохи пізніше');

        $resetResult = $this->checkoutService->resetPromocode();
        $this->promocode = '';
        $this->message = $resetResult ? $successMessage : $errorMessage;

        $response = $resetResult
            ? ResponseService::success($successMessage)
            : ResponseService::error($errorMessage);

        $this->dispatch('checkout-set-property', property: 'promocode', value: '')->to(Checkout::class);
        return $this->dispatch('checkout-promocode-apply-result', response: $response);
    }


    public function render()
    {
        return view('livewire.checkout.promocode');
    }
    public function rendered(){
        $this->dispatch('checkout-promocode-initialized');
    }
}

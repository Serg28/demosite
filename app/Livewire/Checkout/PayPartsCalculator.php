<?php

namespace App\Livewire\Checkout;

use App\Contracts\Installmentable;
use App\Services\Payment\GatewayRegistry;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Показує таблицю розстрочки для MonoPay Parts / Privat Parts.
 * Монтується з wire:key="ppc-{payMethodId}-{total}" → оновлюється при зміні суми.
 */
class PayPartsCalculator extends Component
{
    public string $gatewaySlug = '';
    public float $orderAmount = 0.0;

    public function mount(string $gatewaySlug = '', float $orderAmount = 0.0): void
    {
        $this->gatewaySlug = $gatewaySlug;
        $this->orderAmount = $orderAmount;
    }

    /**
     * @return array<int, array{months: int, monthly: float, total: float}>
     */
    #[Computed]
    public function installments(): array
    {
        if ($this->orderAmount <= 0 || $this->gatewaySlug === '') {
            return [];
        }

        try {
            $registry = app(GatewayRegistry::class);

            if (! $registry->has($this->gatewaySlug)) {
                return [];
            }

            $gateway = $registry->resolve($this->gatewaySlug);

            if (! ($gateway instanceof Installmentable)) {
                return [];
            }

            return $gateway->getInstallments($this->orderAmount);
        } catch (\Throwable) {
            return [];
        }
    }

    public function render(): View
    {
        return view('livewire.checkout.pay-parts-calculator');
    }
}

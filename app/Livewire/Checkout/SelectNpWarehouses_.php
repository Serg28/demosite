<?php

namespace App\Livewire\Checkout;

use App\Livewire\Abstract\SelectSearchable;
use App\Models\City;
use App\Services\Checkout as CheckoutService;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;

class SelectNpWarehouses_ extends SelectSearchable
{

    private CheckoutService $checkoutService;

    public string|int|null $cityId = null;

    public string|null $view = 'livewire.checkout.select-searchable-np-warehouses';

    public function boot(CheckoutService $checkoutService) {
        $this->checkoutService = $checkoutService;
    }

    #[On('checkout-city-changed')]
    public function reload($city_id)
    {
        $this->cityId = $city_id;
    }

    public function rendered(){
        $this->dispatch('checkout-select-np-warehouses-initialized');
    }

    public function options($searchTerm = null): Collection
    {
        //$city = City::find($this->cityId);

        // Проверка наличия города и выполнение запроса только если город найден
        /*if ($city) {
            $query = mb_convert_case($searchTerm, MB_CASE_TITLE, 'UTF-8');

            $warehouses = $this->checkoutService->getWarehouses($city, 'np', $query);

            // Проверка наличия складов
            if ($warehouses) {
                return $warehouses->map(function ($warehouse) {
                    return [
                        'value' => $warehouse['id'],
                        'text' => $warehouse['title'],
                        'escaped_text' => addslashes($warehouse['title']),
                    ];
                });
            }
        }*/

        return collect();
    }

}

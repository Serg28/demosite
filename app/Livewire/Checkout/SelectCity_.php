<?php

namespace App\Livewire\Checkout;

use App\Livewire\Abstract\SelectSearchable;
use App\Models\City;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;

class SelectCity_ extends SelectSearchable
{

    public string|null $view = 'livewire.checkout.select-searchable-cities';

    public function options($searchTerm = null): Collection
    {
        /*if (strlen($searchTerm) > 2) {
            $query = mb_convert_case($searchTerm, MB_CASE_TITLE, 'UTF-8');

            $cities = City::where('title->ua', 'like', $query . '%')
                ->orWhere('title->ru', 'like', $query . '%')
                ->orWhere('title->en', 'like', $query . '%')
                ->select(['id', 'origin'])
                ->orderBy('title->' . App::getLocale(), 'asc')->get();

            return $cities->map(function ($city) {
                return [
                    'value' => $city->id,
                    'text' => $city->t('origin'),
                    'escaped_text' => addslashes($city->t('origin')),
                ];
            });
        }*/
        return collect([]);
    }


    public function select($value, $text): void
    {
        $this->dispatch('checkout-set-property', property: $this->model, value: $value)->to(Checkout::class);
        $this->dispatch('checkout-city-changed', city_id: $value);
    }
    public function rendered(){
        $this->dispatch('checkout-city-select-initialized');
    }
}

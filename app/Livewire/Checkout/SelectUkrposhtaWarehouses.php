<?php

namespace App\Livewire\Checkout;

use App\Livewire\Abstract\SelectSearchableAjax;

class SelectUkrposhtaWarehouses extends SelectSearchableAjax
{
    //Значение по-умолчанию для $model
    public mixed $defaultValue = 0;

    public string|int|null $cityId = null;

    public string|int|null $deliveryId = null;

    public string|null $view = 'livewire.checkout.select-searchable-ukrposhta-warehouses';
}

<?php

namespace App\Livewire\Checkout;

use App\Livewire\Abstract\SelectSearchableAjax;

class SelectNpStreet extends SelectSearchableAjax
{
    //Значение по-умолчанию для $model
    public mixed $defaultValue = 0;

    public string|int|null $cityId = null;

    public string|null $view = 'livewire.checkout.select-searchable-np-street';
}

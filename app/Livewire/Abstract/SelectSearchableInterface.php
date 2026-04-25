<?php

namespace App\Livewire\Abstract;

use Illuminate\Support\Collection;

interface SelectSearchableInterface {

    //example
    /*
    return collect([
        ['value' => '1', 'text' => 'Text 1'],
        ['value' => '2', 'text' => 'Text 2']
    ]);*/
    public function options($searchTerm = null): Collection;
}

<?php

namespace App\Livewire\Components;

use Livewire\Component;

class LoadingSpinner extends Component
{
    public string $size = 'md'; // sm, md, lg
    public string $color = 'blue'; // blue, white, gray
    public ?string $message = null;

    public function render()
    {
        return view('livewire.components.loading-spinner');
    }
}

<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class TwoFactorChallenge extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.two-factor-challenge');
    }
}

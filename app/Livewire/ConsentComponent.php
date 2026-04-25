<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class ConsentComponent extends Component
{
    public bool $consentGiven = false;

    public function mount(): void
    {
        $this->consentGiven = Cookie::has('consent');
    }

    public function giveConsent(): void
    {
        $this->consentGiven = true;
        Cookie::queue('consent', 'true', 525600);
    }

    public function render()
    {
        return view('livewire.consent-component');
    }

    public function rendered()
    {
        $this->dispatch('consent-initialized');
    }
}
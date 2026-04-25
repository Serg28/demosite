<?php

namespace App\Livewire;

use Livewire\Component;

class Recaptcha extends Component
{
    public string $formId;
    public string $hiddenInput;

    public function mount($formId): void
    {
        $this->formId = $formId;
        $this->hiddenInput = $formId . '_token';
    }

    public function rendered()
    {
        $this->dispatch('recaptcha-initialized', formId: $this->formId, hiddenInput: $this->hiddenInput);
    }

    public function render()
    {
        return view('livewire.recaptcha');
    }
}

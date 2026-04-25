<?php

namespace App\Traits;

use Livewire\Attributes\On;

//Трейт для функционала recaptcha. Достаточно в компоненте livewire его добавить. А в форме добавить:
//<form id="{{$formId}}">
//@if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

trait LivewireRecaptchable
{
    public string|null $g_recaptcha_response;

    public mixed $recaptcha;

    public string|null $formId;

    public function bootLivewireRecaptchable(): void
    {
        $this->initRecaptcha();
    }

    //public function hydrate(): void
    public function hydrateLivewireRecaptchable(): void
    {
        if ($this->recaptcha) {
            $this->rulesFromOutside[]['g_recaptcha_response'] =  'required|recaptcha';
        }
    }

    public function initRecaptcha(): void
    {
        $recaptcha = config('recaptcha.active');
        $this->recaptcha = !(($recaptcha === 'false' || $recaptcha === false));
        $this->formId = strtolower(class_basename($this)) . '_component';
    }

    #[On('recaptcha-changed')]
    public function setCaptcha($value): void
    {
        $this->g_recaptcha_response = $value;
    }
}

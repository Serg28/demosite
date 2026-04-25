<?php

namespace App\Livewire;

use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class ModalBlock extends ModalComponent
{
    public string|null $subject = '';
    public string|null $title = '';
    public string|null $text = '';
    public string|null $class = '';

    public static function modalMaxWidth(): string
    {
        return 'succ-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'succ-popup';
    }

    public function render()
    {
        return view('livewire.modalblock');
    }
}

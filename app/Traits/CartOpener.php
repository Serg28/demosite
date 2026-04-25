<?php

namespace App\Traits;

trait CartOpener
{
    public function openCart(): void
    {
        $this->dispatch('openModal', component: 'cart.content');
    }
}

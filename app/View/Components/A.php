<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class A extends Component
{
    public $wire = false;

    public function render(): View
    {
        return view('components.a', ['currentUrl' => currentUrl()]);
    }
}

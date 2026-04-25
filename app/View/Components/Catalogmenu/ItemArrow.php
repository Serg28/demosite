<?php

namespace App\View\Components\Catalogmenu;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemArrow extends Component
{
    public bool|null $condition;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($condition = false)
    {
        $this->condition = $condition;
    }

    public function render(): View
    {
        return view('components.catalogmenu.item-arrow');
    }
}

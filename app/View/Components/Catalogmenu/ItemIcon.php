<?php

namespace App\View\Components\Catalogmenu;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemIcon extends Component
{
    public string $picture;
    public string $alt;
    public string $width;
    public string $height;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($picture = '', $alt = '', $width = 24, $height = 24)
    {
        $this->picture = $picture;
        $this->alt = $alt;
        $this->width = $width;
        $this->height = $height;
    }

    public function render(): View
    {
        return view('components.catalogmenu.item-icon');
    }
}

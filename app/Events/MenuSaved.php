<?php

namespace App\Events;

use App\Models\MenuBase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuSaved
{
    use Dispatchable, SerializesModels;

    public MenuBase $menu;

    public function __construct(MenuBase $menu)
    {
        $this->menu = $menu;
    }
}

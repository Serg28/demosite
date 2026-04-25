<?php

namespace App\Events;

use App\Models\MenuCatalog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuCatalogSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public MenuCatalog $menuCatalog;

    public function __construct(MenuCatalog $menuCatalog)
    {
        $this->menuCatalog = $menuCatalog;
    }

}

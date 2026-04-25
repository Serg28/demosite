<?php

namespace App\Livewire\Catalogmenu;

use App\Models\MenuCatalog;
use App\Models\MenuHeader;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Mobile extends Component
{
    #[Computed(persist: true)]
    private function menuCatalog()
    {
        return (new MenuCatalog())->getSiteMenu(1);
        //return (new Category())->getSiteMenu(1);
    }

    #[Computed(persist: true)]
    private function menuHeader()
    {
        return (new MenuHeader())->getParentMenu(1);
    }

    public function render()
    {
        return view('livewire.catalogmenu.mobile')->with([
            'menuCatalog' => $this->menuCatalog,
            'menuHeader' => $this->menuHeader,
            'currentUrl' => currentUrl(),
        ]);
    }
}

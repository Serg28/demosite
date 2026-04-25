<?php

namespace App\Livewire\Catalogmenu;

use App\Models\MenuCatalog;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: false)]
class HeaderDesktop extends Component
{
    #[Computed]
    private function menu()
    {
        return (new MenuCatalog())->getSiteMenu(1);
    }

    public function rendered()
    {
        $this->dispatch('menu-catalog-header-desktop');
    }

    public function render()
    {
        return view('livewire.catalogmenu.header-desktop')->with('menu', $this->menu())->render();
    }
}

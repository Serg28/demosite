<?php

namespace App\Livewire\Catalogmenu;

//use App\Models\Category;
use App\Models\MenuCatalog;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HomeDesktop extends Component
{
    #[Computed]
    private function menu()
    {
        return (new MenuCatalog())->getSiteMenu(1);
        //return (new Category())->getSiteMenu(1);
    }

    public function rendered()
    {
        $this->dispatch('menu-catalog-home-desktop');
    }

    public function render()
    {
        //return view('livewire.catalogmenu.home-desktop')->with('menu', $this->menu());
        return Cache::tags(['menu_catalog'])->remember('catalogmenu_home_desktop_' . \App::getLocale(), now()->addDay(),
            function () {
                return view('livewire.catalogmenu.home-desktop')->with('menu', $this->menu())->render();
            }
        );
    }
}

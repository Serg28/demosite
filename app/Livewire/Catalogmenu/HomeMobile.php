<?php

namespace App\Livewire\Catalogmenu;

//use App\Models\Category;
use App\Models\MenuCatalog;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HomeMobile extends Component
{
    #[Computed]
    private function menu()
    {
        return (new MenuCatalog())->getSiteMenu(1, true);
        //return (new Category())->getSiteMenu(1, true);
    }

    public function rendered()
    {
        $this->dispatch('menu-catalog-home-mobile');
    }

    public function render()
    {
        return Cache::tags(['menu_catalog'])->remember('catalogmenu_home_mobile_' . \App::getLocale(), now()->addDay(),
            function () {
                return view('livewire.catalogmenu.home-mobile')->with('menu', $this->menu())->render();
            }
        );
    }
}

<?php

namespace App\Livewire\Partials;

use App\Models\MenuHeader;
use App\Models\MenuSidebar;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Topmenu extends Component
{
    private int $parent;

    public function mount(int $parent = 1): void
    {
        $this->parent = $parent;
    }

    #[Computed(persist: true)]
    private function getMenu()
    {
        return (new MenuHeader())->getParentMenu($this->parent);
    }

    public function render()
    {
        $topMenu = $this->getMenu;

        return view('livewire.header.menu', [
            'topMenu' => $topMenu,
            'currentUrl' => currentUrl(),
        ]);
    }

    public function placeholder()
    {
        return view('livewire.header.menu-empty');
    }
}

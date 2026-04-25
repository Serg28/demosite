<?php

namespace App\Livewire\Partials;

use App\Models\MenuSidebar;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SidebarmenuSeocatalog extends Component
{
    public $class = '';

    #[Computed(persist: true)]
    private function getMenu()
    {
        return (new MenuSidebar())->getParentMenu(setting('id-popular-categories-sidebar-menu-node') ?? 1);
    }

    public function render()
    {
        $topMenu = $this->getMenu;

        return view('livewire.partials.sidebarmenu-seocatalog', [
            'topMenu' => $topMenu,
            'currentUrl' => currentUrl()
        ]);
    }

    public function placeholder(): View
    {
        return view($this->view . '-empty');
    }
}

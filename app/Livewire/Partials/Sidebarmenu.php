<?php

namespace App\Livewire\Partials;

use App\Models\MenuSidebar;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Sidebarmenu extends Component
{
    #[Computed(persist: true)]
    private function getMenu()
    {
        return (new MenuSidebar())->getParentMenu(2);
    }

    public function render()
    {
        $topMenu = $this->getMenu;

        return view('livewire.partials.sidebarmenu', [
            'topMenu' => $topMenu,
            'currentUrl' => currentUrl()
        ]);
    }

    public function placeholder(): View
    {
        return view($this->view . '-empty');
    }
}

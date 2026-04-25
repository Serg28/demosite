<?php

namespace App\Livewire\Partials;

use App\Models\MenuFooter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Footermenu extends Component
{
    private int $parent;

    private string $view;

    public function mount($view = 'livewire.footer.menu', $parent = 1): void
    {
        $this->parent = $parent;
        $this->view = $view;
    }

    #[Computed(persist: true)]
    private function getMenu()
    {
        return (new MenuFooter())->getParentMenu($this->parent);
    }

    public function render()
    {
        $footerMenu = $this->getMenu;

        return view($this->view, [
            'footerMenu' => $footerMenu,
            'currentUrl' => currentUrl(),
        ]);
    }
}
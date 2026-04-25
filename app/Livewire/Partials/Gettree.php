<?php

namespace App\Livewire\Partials;

use App\Models\Tree;
use Livewire\Component;

class Gettree extends Component
{

    private int $parent;

    private string $view;

    private bool $islazy;

    public function mount($islazy = false, $view = 'livewire.partials.gettree', $parent = null): void
    {
        $this->parent = $parent;
        $this->view = $view;
        $this->islazy = $islazy;
    }

    public function render()
    {
        $tree = ($this->parent) ? Tree::descendantsOf($this->parent)->toTree() : [];

        return view($this->view, [
            'tree' => $tree,
            'currentUrl' => ($this->islazy) ? (request()->header('referer') ?? request()->url()) : request()->url(),
        ]);
    }
}

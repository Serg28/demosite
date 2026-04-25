<?php

namespace App\Livewire\Category;

class FilterParentProducts extends FilterProducts
{

    public function getView()
    {
        return $this->page->getTemplate('partials.livewire-filter-parent-products');
    }
}

<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Component;

class Page extends Component
{
    public Category $category;

    #[Url]
    public array $filters = [];

    #[Url]
    public int $page = 1;

    #[Url]
    public string $sort_by = 'priority';

    #[Url]
    public string $sort_dir = 'desc';

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function updateFilters(array $newFilters): void
    {
        $this->filters = $newFilters;
        $this->page = 1;
    }

    public function updateSort(string $sortBy, string $sortDir): void
    {
        $this->sort_by = $sortBy;
        $this->sort_dir = $sortDir;
        $this->page = 1;
    }

    public function render()
    {
        return view('livewire.catalog.page');
    }
}

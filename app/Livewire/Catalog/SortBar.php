<?php

namespace App\Livewire\Catalog;

use Livewire\Component;

class SortBar extends Component
{
    public string $sortBy = 'priority';

    public string $sortDir = 'desc';

    public function mount(string $sortBy = 'priority', string $sortDir = 'desc'): void
    {
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
    }

    public function updateSort(string $sortBy, string $sortDir = 'desc'): void
    {
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;

        $this->dispatch('sortUpdated', sortBy: $sortBy, sortDir: $sortDir);
    }

    public function toggleDirection(): void
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->dispatch('sortUpdated', sortBy: $this->sortBy, sortDir: $this->sortDir);
    }

    public function render()
    {
        return view('livewire.catalog.sort-bar');
    }
}

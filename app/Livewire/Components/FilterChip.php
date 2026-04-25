<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FilterChip extends Component
{
    public string $label;
    public string $value;
    public ?string $filterType = null; // category, price_range, brand, etc.

    public function mount(string $label, string $value, ?string $filterType = null): void
    {
        $this->label = $label;
        $this->value = $value;
        $this->filterType = $filterType;
    }

    public function remove(): void
    {
        $this->dispatch('filterRemoved', type: $this->filterType, value: $this->value);
    }

    public function render()
    {
        return view('livewire.components.filter-chip');
    }
}

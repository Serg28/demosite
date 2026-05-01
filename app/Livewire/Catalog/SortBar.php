<?php

namespace App\Livewire\Catalog;

use Livewire\Attributes\On;
use Livewire\Component;

class SortBar extends Component
{
    public string $sortBy = 'priority';

    public string $sortDir = 'desc';

    public function mount(string $sortBy = 'priority', string $sortDir = 'desc'): void
    {
        $this->sortBy  = $sortBy;
        $this->sortDir = $sortDir;
    }

    #[On('sort-changed')]
    public function onSortChanged(string $sortKey = ''): void
    {
        [$this->sortBy, $this->sortDir] = $this->resolveSortKey($sortKey);
    }

    /** @return array{string, string} */
    private function resolveSortKey(string $sortKey): array
    {
        foreach (config('catalog.sort_options') as $option) {
            if ($option['url_key'] === ($sortKey ?: null)) {
                return [$option['key'], $option['dir']];
            }
        }

        $default = config('catalog.sort_options.0');

        return [$default['key'], $default['dir']];
    }

    /** @return array<int, array<string, mixed>> */
    public function getSortOptionsProperty(): array
    {
        return array_map(function (array $option) {
            return array_merge($option, [
                'is_active' => $option['key'] === $this->sortBy && $option['dir'] === $this->sortDir,
            ]);
        }, config('catalog.sort_options', []));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.catalog.sort-bar');
    }
}

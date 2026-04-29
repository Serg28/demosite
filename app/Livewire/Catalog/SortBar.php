<?php

namespace App\Livewire\Catalog;

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

    /** @return array<int, array<string, mixed>> */
    public function getSortOptionsProperty(): array
    {
        return array_map(function (array $option) {
            $params = request()->query();
            if ($option['url_key']) {
                $params['sort'] = $option['url_key'];
            } else {
                unset($params['sort']);
            }

            $query = $params ? '?' . http_build_query($params) : '';

            return array_merge($option, [
                'url'       => url()->current() . $query,
                'is_active' => $option['key'] === $this->sortBy && $option['dir'] === $this->sortDir,
            ]);
        }, config('catalog.sort_options', []));
    }

    public function render()
    {
        return view('livewire.catalog.sort-bar');
    }
}

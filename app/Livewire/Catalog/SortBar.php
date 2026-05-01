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
        // currentUrl() handles livewire/update requests by falling back to Referer header,
        // so the filter path segments (e.g. /color=red/) are always included correctly.
        $currentUrl = currentUrl();
        $parsed = parse_url($currentUrl);
        parse_str($parsed['query'] ?? '', $currentQuery);
        $basePath = $parsed['path'] ?? '/';

        return array_map(function (array $option) use ($basePath, $currentQuery) {
            $params = $currentQuery;
            unset($params['page']); // sort change resets pagination

            if ($option['url_key']) {
                $params['sort'] = $option['url_key'];
            } else {
                unset($params['sort']);
            }

            $query = $params ? '?' . http_build_query($params) : '';

            return array_merge($option, [
                'url'       => $basePath . $query,
                'is_active' => $option['key'] === $this->sortBy && $option['dir'] === $this->sortDir,
            ]);
        }, config('catalog.sort_options', []));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.catalog.sort-bar');
    }
}

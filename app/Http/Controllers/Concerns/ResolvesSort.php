<?php

namespace App\Http\Controllers\Concerns;

trait ResolvesSort
{
    /** @return array{string, string} [sortBy, sortDir] */
    private function resolveSortParam(?string $sortParam): array
    {
        foreach (config('catalog.sort_options') as $option) {
            if ($option['url_key'] === $sortParam) {
                return [$option['key'], $option['dir']];
            }
        }

        $default = config('catalog.sort_options.0');

        return [$default['key'], $default['dir']];
    }
}

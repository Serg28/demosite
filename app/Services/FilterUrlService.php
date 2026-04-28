<?php

namespace App\Services;

use App\Models\Category;

/**
 * FilterUrlService — parsing and building SEO filter path URLs.
 *
 * URL format: /catalog/{category}/{char-slug}={opt-slug1},{opt-slug2}/{char-slug2}={min}-{max}/
 */
class FilterUrlService
{
    /**
     * Parse filter path segments into a structured filters array.
     *
     * Input:  "color=red,blue/price=100-500"
     * Output: ['characteristics' => [charId => [optId1, optId2]], 'min_price' => 100, 'max_price' => 500]
     *
     * @param  array<string, array{char_id: int, is_range_type: bool, options: array<string, int>}>  $slugMap
     * @return array{characteristics: array<int, int[]>, min_price: int|null, max_price: int|null}
     */
    public function parseFilterPath(string $path, array $slugMap): array
    {
        $result = ['characteristics' => [], 'min_price' => null, 'max_price' => null];

        foreach (array_filter(explode('/', trim($path, '/'))) as $segment) {
            if (! str_contains($segment, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $segment, 2);

            if ($key === 'price') {
                [$min, $max] = array_pad(explode('-', $value, 2), 2, null);
                $result['min_price'] = is_numeric($min) ? (int) $min : null;
                $result['max_price'] = is_numeric($max) ? (int) $max : null;

                continue;
            }

            if (! isset($slugMap[$key])) {
                continue;
            }

            $charData = $slugMap[$key];

            if ($charData['is_range_type']) {
                [$min, $max] = array_pad(explode('-', $value, 2), 2, null);
                $result['characteristics'][$charData['char_id']] = [
                    'type' => 'range',
                    'min' => is_numeric($min) ? (int) $min : null,
                    'max' => is_numeric($max) ? (int) $max : null,
                ];

                continue;
            }

            $optSlugs = array_filter(explode(',', $value));
            $optIds = array_values(array_filter(
                array_map(fn ($s) => $charData['options'][$s] ?? null, $optSlugs)
            ));

            if (! empty($optIds)) {
                $result['characteristics'][$charData['char_id']] = $optIds;
            }
        }

        return $result;
    }

    /**
     * Build a toggle URL for a checkbox option (adds if absent, removes if present).
     *
     * @param  string  $basePath  e.g. "/catalog/phones"
     * @param  string  $currentFiltersPath  e.g. "color=red/"
     */
    public function buildOptionUrl(string $basePath, string $currentFiltersPath, string $charSlug, string $optSlug): string
    {
        $segments = $this->parseSegments($currentFiltersPath);

        if (isset($segments[$charSlug])) {
            $existing = array_filter(explode(',', $segments[$charSlug]));

            if (in_array($optSlug, $existing, true)) {
                $existing = array_values(array_filter($existing, fn ($s) => $s !== $optSlug));
            } else {
                $existing[] = $optSlug;
            }

            if (empty($existing)) {
                unset($segments[$charSlug]);
            } else {
                $segments[$charSlug] = implode(',', $existing);
            }
        } else {
            $segments[$charSlug] = $optSlug;
        }

        return $this->buildPath($basePath, $segments);
    }

    /**
     * Build a range URL segment (update or remove).
     */
    public function buildRangeUrl(string $basePath, string $currentFiltersPath, string $charSlug, ?int $min, ?int $max): string
    {
        $segments = $this->parseSegments($currentFiltersPath);

        if ($min === null && $max === null) {
            unset($segments[$charSlug]);
        } else {
            $segments[$charSlug] = "{$min}-{$max}";
        }

        return $this->buildPath($basePath, $segments);
    }

    /**
     * Build a URL with ALL filters cleared.
     */
    public function buildClearUrl(string $basePath): string
    {
        return rtrim($basePath, '/').'/';
    }

    /**
     * Build the slug map for a category using a single eager-loaded query.
     *
     * @return array<string, array{char_id: int, is_range_type: bool, options: array<string, int>}>
     */
    public function buildSlugMap(Category $category): array
    {
        $chars = $category->characteristics()
            ->where('is_active', true)
            ->with(['options:id,characteristic_id,slug'])
            ->get(['characteristics.id', 'characteristics.slug', 'characteristics.is_range_type']);

        $map = [];
        foreach ($chars as $char) {
            $optMap = [];
            foreach ($char->options as $opt) {
                $optMap[$opt->slug] = $opt->id;
            }
            $map[$char->slug] = [
                'char_id' => $char->id,
                'is_range_type' => (bool) $char->is_range_type,
                'options' => $optMap,
            ];
        }

        return $map;
    }

    /** @return array<string, string> */
    private function parseSegments(string $filtersPath): array
    {
        $segments = [];
        foreach (array_filter(explode('/', trim($filtersPath, '/'))) as $segment) {
            if (str_contains($segment, '=')) {
                [$key, $value] = explode('=', $segment, 2);
                $segments[$key] = $value;
            }
        }

        return $segments;
    }

    /** @param  array<string, string>  $segments */
    private function buildPath(string $basePath, array $segments): string
    {
        $base = rtrim($basePath, '/');

        if (empty($segments)) {
            return $base.'/';
        }

        $parts = [];
        foreach ($segments as $key => $value) {
            $parts[] = "{$key}={$value}";
        }

        return $base.'/'.implode('/', $parts).'/';
    }
}

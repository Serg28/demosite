<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TypeSense Search Limits
    |--------------------------------------------------------------------------
    |
    | limit_hits caps how many documents TypeSense considers per query.
    | Default in TypeSense is 500 — far too low for real catalogs.
    | At 24/page this covers up to 416 pages. TypeSense hard cap for
    | offset-based pagination is 10 000; cursor (search_after) is needed beyond.
    |
    */
    'typesense' => [
        'limit_hits' => (int) env('CATALOG_TYPESENSE_LIMIT_HITS', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sort Options
    |--------------------------------------------------------------------------
    |
    | Used by SortBar component and TypeSenseService.
    | Add model-specific overrides by passing sort_options via mount().
    | 'key' must match a case in TypeSenseService::applyScoutSorting().
    |
    */
    'sort_options' => [
        ['key' => 'priority', 'dir' => 'desc', 'url_key' => null,        'label' => 'За популярністю'],
        ['key' => 'price',    'dir' => 'asc',  'url_key' => 'priceup',   'label' => 'Дешевше'],
        ['key' => 'price',    'dir' => 'desc', 'url_key' => 'pricedown', 'label' => 'Дорожче'],
        ['key' => 'newest',   'dir' => 'desc', 'url_key' => 'newest',    'label' => 'Новинки'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => (int) env('CATALOG_PER_PAGE', 24),

    /*
    |--------------------------------------------------------------------------
    | Facet Display
    |--------------------------------------------------------------------------
    |
    | show_limit    — options visible before "Show more" button appears.
    | search_min    — min number of options required to show the search input.
    |
    */
    'facets' => [
        'show_limit'        => 8,
        'search_min'        => 8,
        'sort_active_first' => (bool) env('CATALOG_FACETS_SORT_ACTIVE_FIRST', false),
    ],

];

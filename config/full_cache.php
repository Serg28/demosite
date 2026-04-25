<?php

return [

    /* switch enabled */
    'enabled' => env('FULL_CACHE', false),

    /* list urls that will NOT be cached */
    'notWillCacheUrl' => ['api', 'admin', 'login', 'docs/1.0/overview', 'auth', '/auth/login', '/profile', '/livewire/update', 'checkout', 'payment/confirm'],

    /* list urls that will be cached, if empty array then will be cached all urls*/
    'cacheUrl' => [], //example ['admin/tree', 'admin/news']

    /* monitors the tags */
    'tagsCache' => [
        'tree',
        'category_tree',
        'page_full_cache',
        'category', 'elasticsearch', 'products'
    ],

    /* list sessions that will be used in key cache */
    'sessionForCache' => [],

    /* list cookies that will be used in key cache */
    'cookiesForCache' => ['consent'],
];

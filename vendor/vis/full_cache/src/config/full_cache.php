<?php

return [

    /* switch enabled */
    'enabled' => false,

    /* list urls that will NOT be cached */
    'notWillCacheUrl' => ['admin', 'login'],

    /* list urls that will be cached, if empty array then will be cached all urls*/
    'cacheUrl' => [], //example ['admin/tree', 'admin/news']

    /* monitors the tags */
    'tagsCache' => [
        'tree',
        'category_tree'
    ],

    /* list sessions that will be used in key cache */
    'sessionForCache' => [],

    /* list cookies that will be used in key cache */
    'cookiesForCache' => [],
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search "driver" that will be used by
    | Laravel Scout. You are free to specify any of the other search engines
    | that are supported by Scout here.
    |
    */

    'driver' => env('SCOUT_DRIVER', 'typesense'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" this
    | will greatly improve the performance of database operations. You are
    | free to decide what your application needs.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if Scout automatically syncs the
    | database records into the search engine. By default this is set to
    | true, which means Scout will automatically sync records. You may
    | want to set this to false for local development.
    |
    */

    'sync' => env('SCOUT_SYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options allow you to control the maximum chunk size when syncing
    | the search index with your database. This allows you to fine-tune
    | indexing performance for large datasets.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows to control whether to keep soft deleted records
    | in the search index. Maintaining soft deleted records can be useful
    | if your application still needs to search for the records to restore them.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a simple and
    | reliable cloud search service that works great with Scout for indexing
    | all of your Eloquent models. Just plug in your config here.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Meilisearch settings. Meilisearch is an
    | open source search engine with intuitive API and SDKs.
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Typesense Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Typesense settings.
    |
    */

    'typesense' => [
        'client-settings' => [
            'api_key' => env('TYPESENSE_API_KEY', 'demo_api_key_12345'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'typesense'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'path' => env('TYPESENSE_PATH', ''),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'nearest_node' => [
                'host' => env('TYPESENSE_HOST', 'typesense'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
            'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
            'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
            'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
            'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
        ],
        'model-settings' => [
            \App\Models\Product::class => [
                'collection-schema' => [
                    'fields' => [
                        ['name' => 'id',          'type' => 'string'],
                        ['name' => 'title',       'type' => 'string'],
                        ['name' => 'description', 'type' => 'string', 'optional' => true],
                        ['name' => 'price',       'type' => 'float'],
                        ['name' => 'price_old',   'type' => 'float',  'optional' => true],
                        ['name' => 'category_id', 'type' => 'int32',  'optional' => true],
                        ['name' => 'slug',        'type' => 'string'],
                        ['name' => 'is_active',   'type' => 'bool'],
                        ['name' => 'in_stock',    'type' => 'bool'],
                        ['name' => 'priority',    'type' => 'int32'],
                        ['name' => 'created_at',  'type' => 'int64'],
                    ],
                    'default_sorting_field' => 'priority',
                ],
                'search-parameters' => [
                    'query_by'   => 'title,description',
                    'limit_hits' => 10000,
                ],
            ],
        ],
    ],

];

<?php

namespace App;

use Novius\ScoutElastic\IndexConfigurator;

class ProductsIndexConfigurator extends IndexConfigurator
{
    /**
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'filter' => [
                'russian_stop' => [
                    'type' => 'stop',
                    'stopwords' => '_russian_',
                ],
                'shingle' => [
                    'type' => 'shingle',
                ],
                'mynGram' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 3,
                    'max_gram' => 20,
                ],
                'length_filter' => [
                    'type' => 'length',
                    'min' => 1,
                ],
                'russian_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'russian',
                ],
                'english_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'english',
                ],
                'codeGram' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 5,
                    'max_gram' => 20,
                ],
            ],
            'analyzer' => [
                'title' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'trim',
                        //'russian_stemmer',
                        //'english_stemmer',
                        'russian_stop',
                    ],
                ],
                //'partial_title' => [
                //    'type' => 'custom',
                //    'tokenizer' => 'standard',
                //    'filter' => [
                //        'lowercase',
                //        'trim',
                //        'russian_stemmer',
                //        'english_stemmer',
                //        'russian_stop',
                //        'codeGram',
                //        'length_filter',
                //    ],
                //],
            ],
        ],
    ];

    protected $defaultMapping = [
        'properties' => [
            'title' => [
                'type' => 'text',
                'analyzer' => 'title',
            ],
            'code' => [
                'type' => 'keyword',
            ],
            //'part_number' => [
            //    'type' => 'keyword',
            //],
            //'table_part_number' => [
            //    'type' => 'text',
            //    'analyzer' => 'title',
            //],
            'is_active' => [
                'type' => 'integer',
            ],
            'has_picture' => [
                'type' => 'integer',
            ],
            //'is_universal' => [
            //    'type' => 'integer',
            //],
            //'is_new' => [
            //    'type' => 'integer',
            //],
            'is_sale' => [
                'type' => 'integer',
            ],
            'quantity' => [
                'type' => 'integer',
            ],
            'price' => [
                'type' => 'integer',
            ],
            'category' => [
                //'type' => 'integer',
                'type' => 'keyword',
            ],
            'top_category' => [
                'type' => 'integer',
            ],
            'options' => [
                'type' => 'flattened',
            ],
            'created_at' => [
                'type' => 'date',
            ],
            'updated_at' => [
                'type' => 'date',
            ],
            'product_status_id' => [
                'type' => 'integer',
            ],
            'count_views' => [
                'type' => 'integer',
            ],
            //'count_add_to_cart' => [
            //    'type' => 'integer',
            //],
            //'partial_title' => [
            //    'type' => 'text',
            //    'analyzer' => 'partial_title',
            //],
        ],
    ];
}

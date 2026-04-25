<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ReindexProductCurrentIndex extends Command
{
    use DispatchesJobs;

    private static Client $client;
    protected $signature = 'search:reindex_current_index';

    protected $description = 'Indexes all products to Elasticsearch';

    private $elasticsearch;

    public function __construct(Client $elasticsearch, Product $product)
    {
        ini_set('memory_limit', '512M');
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
        $this->product = $product;
    }

    /*С делением на чанки и без простоя*/
    public function handle(): void
    {
        try {
            $this->info('Indexing all articles. This might take a while...');
            // Получаем текущий индекс
            $currentIndex = $this->product->getSearchIndex();

            // Получаем все товары
            $products = Product::cursor();

            // Разбиваем товары на части для параллельной обработки
            $productChunks = $products->chunk(1000);

            // Начинаем измерение времени
            $startTime = microtime(true);

            foreach ($productChunks as $chunk) {
                // Подготавливаем пакетный запрос
                $bulkRequest = [];

                foreach ($chunk as $article) {
                    $bulkRequest[] = [
                        'index' => [
                            '_index' => $currentIndex, // Используем новый индекс
                            '_type' => $article->getSearchType(),
                            '_id' => $article->getKey(),
                        ],
                    ];

                    $bulkRequest[] = $article->toSearchArray();
                }

                // Отправляем пакетный запрос в Elasticsearch
                $params = [
                    'refresh' => 'false',
                    'body' => $bulkRequest,
                ];
                $this->elasticsearch->bulk($params);
                $this->output->write('.');
            }

            // Рассчитываем прошедшее время
            $elapsedTime = microtime(true) - $startTime;

            $this->info('Done! Elapsed time: ' . $elapsedTime . ' seconds');
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
    //----

    private function settings(Product $product): array
    {
        return [
            'index' => $product->getSearchIndex(),
            /*'body' => [
                'settings' => [
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
                        ],
                        'analyzer' => [
                            'title' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    //'asciifolding', //добавлено
                                    'lowercase',
                                    //'mynGram',  //из коробки
                                    //'length_filter',  //из коробки
                                    'trim',
                                    'russian_stemmer',  //из коробки
                                    'english_stemmer',  //из коробки
                                    'russian_stop',  //из коробки
                                ],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'title',
                        ],
                        'code' => [
                            //'type' => 'text', //из коробки
                            //'analyzer' => 'title', //из коробки
                            'type' => 'keyword' //добавлено
                        ],
                        'is_active' => [
                            'type' => 'integer',
                        ],
                        'quantity' => [
                            'type' => 'integer',
                        ],
                        'price' => [
                            'type' => 'integer',
                        ],
                        'category' => [
                            'type' => 'integer',
                        ],
                        'options' => [
                            'type' => 'flattened',
                        ],

                    ],
                ],
            ],*/


            'body' => [
                'settings' => [
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
                                'min_gram' => 3,
                                'max_gram' => 20,
                            ],
                        ],
                        //--
                        'char_filter' => [
                            'replace_dash_char_filter' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    "- => _"
                                ]
                            ]
                        ],
                        //--
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
                                    //--
                                    'char_filter' => [
                                        'replace_dash_char_filter'
                                    ],
                                    //--
                                ],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'title',
                        ],
                        'code' => [
                            'type' => 'keyword',
                        ],
                        'is_active' => [
                            'type' => 'integer',
                        ],
                        'quantity' => [
                            'type' => 'integer',
                        ],
                        'price' => [
                            'type' => 'integer',
                        ],
                        'category' => [
                            'type' => 'integer',
                        ],
                        'top_category' => [
                            'type' => 'integer',
                        ],
                        'options' => [
                            'type' => 'flattened',
                        ],
                        /*'partial_title' => [
                            'type' => 'text',
                            'analyzer' => 'partial_title',
                        ],*/
                    ],
                ],
            ],
        ];
    }
}

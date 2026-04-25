<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ReindexProduct extends Command
{
    use DispatchesJobs;

    private static Client $client;
    protected $signature = 'search:reindex';

    protected $description = 'Indexes all products to Elasticsearch';

    private $elasticsearch;

    public function __construct(Client $elasticsearch, Product $product)
    {
        ini_set('memory_limit', '512M');
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
        $this->product = $product;
    }

    //Из коробки - вариант с удалением индекса перед началом переиндексации. Данные в таком случае становятся недоступными на момент действия команды

    /*public function handle(): void
    {
        $this->info('Indexing all articles. This might take a while...');

        try {
            $this->elasticsearch->indices()->delete(['index' => $this->product->getSearchIndex()]);
        } catch (\Exception $e) {
//            until we catch the exception
        }

        $this->elasticsearch->indices()->create($this->settings($this->product));

        foreach (Product::cursor() as $article) {
            $this->elasticsearch->index([
                'index' => $article->getSearchIndex(),
                'type' => $article->getSearchType(),
                'id' => $article->getKey(),
                'body' => $article->toSearchArray(),
            ]);

            $this->output->write('.');
        }

        $this->info('\nDone!');
    }*/

    /*С делением на чанки и без простоя*/
    public function handle(): void
    {
        try {
            $this->info('Indexing all articles. This might take a while...');
            //dd($this->elasticsearch->cat()->indices(['format' => 'json']));

            // Получаем текущий индекс
            $currentIndex = $this->product->getSearchIndex();
            $newIndex = $currentIndex . '_' . time(); // Создаем новое имя для нового индекса

            // Создаем новый индекс
            try {
                $this->elasticsearch->indices()->create([
                    'index' => $newIndex,
                    'body' => $this->settings($this->product)['body'],
                ]);
            } catch (\Exception $e) {
                $this->error('Error occurred while creating index: ' . $e->getMessage());
                return;
            }

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
                            '_index' => $newIndex, // Используем новый индекс
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

            // Присваиваем новому индексу алиас со значением старого индекса
            try {
                $this->elasticsearch->indices()->putAlias([
                    'index' => $newIndex,
                    'name' => $currentIndex,
                ]);
            } catch (\Exception $e) {
                $this->error('Error occurred while assigning alias: ' . $e->getMessage());
                return;
            }

            // Получаем список всех индексов
            $indices = $this->elasticsearch->indices()->get(['index' => '*']);

            //Удаляем все индексы с именем таблицы + кроме нового
            foreach ($indices as $index => $data) {
                // Проверяем, содержит ли имя индекса значение $currentIndex
                if ($index !== $newIndex && strpos($index, $currentIndex) !== false) {
                    // Удаляем индекс
                    try {
                        $this->elasticsearch->indices()->delete(['index' => $index]);
                        $this->info("Index $index deleted.");
                    } catch (\Exception $e) {
                        $this->error("Failed to delete index $index: " . $e->getMessage());
                    }
                }
            }

            // Активируем индекс для быстрого первого обращения к нему
            $this->elasticsearch->search([
                'index' => $newIndex,
                'body' => [
                    'query' => [
                        'match_all' => new \stdClass(),
                    ],
                ],
            ]);

            // Рассчитываем прошедшее время
            $elapsedTime = microtime(true) - $startTime;

            $this->info('Done! Elapsed time: ' . $elapsedTime . ' seconds');
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
    //----

    /**
     * Обновляем в индексе ВСЕ товары по указанному массиву поле=значение
     * @param array $fields
     */
    public function updateIndexForAll(array $fields): void
    {
        // Disable refresh interval to speed up the indexing process
        $this->elasticsearch->indices()->putSettings([
            'index' => ['name' => $this->product->getSearchIndex()],
            'body' => [
                'refresh_interval' => '-1',
            ],
        ]);

        // Update fields for all products in the index
        $this->elasticsearch->updateByQuery([
            'index' => ['name' => $this->product->getSearchIndex()],
            'type' => $this->product->getSearchType(),
            'body' => [
                'script' => [
                    'source' => $this->generateScript($fields),
                ],
            ],
        ]);

        // Re-enable the refresh interval
        $this->elasticsearch->indices()->putSettings([
            'index' => ['name' => $this->product->getSearchIndex()],
            'body' => [
                'refresh_interval' => '1s',
            ],
        ]);
    }

    private function generateScript(array $fields): string
    {
        $script = '';

        foreach ($fields as $field => $value) {
            $script .= "ctx._source.{$field} = params.{$field};";
        }

        return $script;
    }

    //--

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
                            'partial_title' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'trim',
                                    'russian_stemmer',
                                    'english_stemmer',
                                    'russian_stop',
                                    //'mynGram',
                                    'codeGram',
                                    'length_filter',
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
                        'partial_title' => [
                            'type' => 'text',
                            'analyzer' => 'partial_title',
                        ],
                    ],
                ],
            ],
        ];
    }
}

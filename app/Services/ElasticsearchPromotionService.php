<?php

namespace App\Services;

use App\Models\Characteristic;
use App\Models\Product;
use App\Services\Filters\FilterUniversal;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Async\Pool;
use stdClass;

/**
* ElasticsearchPromotionService класс для фильтрации товаров в разделе Акции
 * Класс ищет и фильтрует товары на основе заданных характеристик, используемых в разделе Акции
 * По-умолчанию - это характеристика Бренд
 */

class ElasticsearchPromotionService
{
    private Client $elasticsearch;

    private Product $productModel;

    private FilterUniversal $filter;

    private Collection $characteristics;

    private string $options = 'options.';

    private $page;

    public array $defaultCharacteristics = [
        15010 //Brand
    ];

    public array $productIds;

    private bool $showAll;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    // Если у модели есть метод characteristics(), используем его
    // Иначе используем характеристики по-умолчанию
    public function getCharacteristics(Model $model = null)
    {
        if ($model && method_exists($model, 'characteristics')) {
            return $model->characteristics();
        }
        return Characteristic::whereIn('id', $this->defaultCharacteristics)->active();
    }

    public function getCharacteristicsQuery(Model $model = null): Collection
    {
        return $this->getCharacteristics($model)->pluck('characteristics.id');
    }


    public function filterPromotions(Model $page, FilterUniversal $filter, ?array $productIds): array
    {
        return $this->filter($page, $filter, $productIds);
    }

    public function filter(Model $page, FilterUniversal $filter, ?array $productIds, $showAll = false): array
    {
        $this->filter = $filter;
        $this->productIds = $productIds;
        $this->showAll = $showAll;

        $this->characteristics = $this->getCharacteristicsQuery($page);

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(config('services.search.hosts'))
            ->build();

        return $this->buildCollectionAfterFilter(
            $this->filterOnElasticsearch($filter)
        );
    }

    //Мин и макс цена вычисляется без учета фильтра цены
    private function filterOnElasticsearch(FilterUniversal $filter): array
    {
        // Запрос для получения результатов по всем фильтрам
        $query1 = [
            'index' => $this->productModel->getSearchIndex(),
            'type' => $this->productModel->getSearchType(),
            'body' => [
                'size' => 1000,
                'query' => [
                    'bool' => [
                        'must' => $this->prepareQuery($filter),
                    ],
                ],
                'aggregations' => [
                    'all_products' => [
                        'global' => new stdClass(),
                        'aggregations' => $this->prepareAggregations($filter),
                    ],
                    'all_options' => [
                        'global' => new stdClass(),
                        'aggregations' => $this->prepareAggregationsFilter(),
                    ],
                    'max_price' => [
                        'max' => [
                            'field' => 'price',
                        ],
                    ],
                    'min_price' => [
                        'min' => [
                            'field' => 'price',
                        ],
                    ],
                ],
                //'_source' => ['id', 'code']
            ],
        ];

        // Запрос для получения статистики цен без учета фильтра цены
        $query2 = [
            'index' => $this->productModel->getSearchIndex(),
            'type' => $this->productModel->getSearchType(),
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $this->prepareQuery($filter, false), // Условия фильтрации без цены
                    ],
                ],
                'aggregations' => [
                    'price_stats' => [
                        'stats' => ['field' => 'price'],
                    ],
                ],
            ],
        ];

        // Выполнение msearch запроса
        $response = $this->elasticsearch->msearch([
            'body' => [
                ['index' => $query1['index'], 'type' => $query1['type']],
                $query1['body'],
                ['index' => $query2['index'], 'type' => $query2['type']],
                $query2['body'],
            ],
        ]);

        // Обработка результатов

        // Извлечение результатов первого запроса
        $result1 = $response['responses'][0];

        // Извлечение результатов второго запроса
        $result2 = $response['responses'][1]['aggregations']['price_stats'];

        // Добавление price_stats в результат 0
        $result1['aggregations']['price_stats'] = $result2;

        return $result1;
    }

    private function prepareQuery(FilterUniversal $filter, $withPrices = true): array
    {
        $filtersIds = $filter->getSelectedFilterIds();
        unset($filtersIds[0]);

        $must = [
            [
                'terms' => ['is_active' => [1]],
            ],
        ];

        if (isset($filter->getFilter()['category'])) {
            $must[] = [
                'terms' => ['top_category' => array_values($filter->getFilter()['category'])],
            ];
        }


        if (count($this->productIds)>0) {
            $must[] = ['terms' => ['_id' => $this->productIds]];
        }


        if ($filter->minPrice() && $filter->maxPrice() && $withPrices) {
            $must[] = [
                'range' => [
                    'price' => [
                        'gte' => $this->convertCurrencyForElastic($filter->minPrice()),
                        'lte' => $this->convertCurrencyForElastic($filter->maxPrice()),
                    ]
                ],
            ];
        }

        foreach ($filtersIds as $category => $options) {
            $must[] = [
                'terms' => [$this->options . $category => $options],
            ];
        }

        return $must;
    }

    private function prepareAggregationsFilter(): array
    {
        $aggregations = [];

        foreach ($this->characteristics as $id) {
            $characteristic = $this->options . $id;

            $aggregations[$characteristic] = [
                'filter' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => ['is_active' => 1],
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    $characteristic => [
                        'terms' => [
                            'field' => $characteristic,
                            'size' => 1000,
                        ],
                    ],
                    'category' => [
                        'terms' => [
                            'field' => 'top_category',
                            'size' => 1000,
                        ]
                    ]
                ],

            ];
            if (count($this->productIds)>0) {
                $aggregations[$characteristic]['filter']['bool']['must'] = ['terms' => ['_id' => $this->productIds]];
            }
        }

        return $aggregations;
    }

    private function prepareAggregations(FilterUniversal $filter): array
    {
        $filtersIds = $filter->getSelectedFilterIds();

        unset($filtersIds[0]);

        $aggregations = [];

        foreach ($this->characteristics as $id) {
            $characteristic = $this->options . $id;

            $must = $this->prepareFilter($id, $filtersIds, $filter);

            $aggregations[$characteristic] = [
                'filter' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggregations' => [
                    $characteristic => [
                        'terms' => [
                            'field' => $characteristic,
                            'size' => 1000,
                            //   "min_doc_count" => 0
                        ],
                    ],
                    //--
                    'category' => [
                        'terms' => [
                            'field' => 'top_category',
                            'size' => 1000,
                        ]
                    ]
                    //--
                ],

            ];
        }

        return $aggregations;
    }

    private function prepareFilter(int $id, array $filtersIds, FilterUniversal $filter): array
    {
        $must = [
            [
                'term' => ['is_active' => 1],
            ],
        ];

        if (isset($filter->getFilter()['category'])) {
            $must[] = [
                'terms' => ['top_category' => array_values($filter->getFilter()['category'])],
            ];
        }
        if (count($this->productIds)>0) {
            $must[] = ['terms' => ['_id' => $this->productIds]];
        }

        if ($filter->minPrice() && $filter->maxPrice()) {
            $must[] = [
                'range' => [
                    'price' => [
                        'gte' => $this->convertCurrencyForElastic($filter->minPrice()),
                        'lte' => $this->convertCurrencyForElastic($filter->maxPrice()),
                    ]
                ],
            ];
        }

        foreach ($filtersIds as $idCharacteristic => $filterArray) {
            if ($idCharacteristic != $id) {
                $must[] = [
                    'terms' => [$this->options . $idCharacteristic => $filterArray],
                ];
            }
        }
        return $must;
    }

    //Синхронно
    private function buildCollectionAfterFilter(array $items): array
    {
        $ids = Arr::pluck($items['hits']['hits'], '_id');

        $buckets = [];
        $bucketsFilter = [];

        foreach ($items['aggregations']['all_products'] as $item) {
            if (is_array($item)) {
                foreach ($item as $options) {
                    if (isset($options['buckets'])) {
                        foreach ($options['buckets'] as $bucket) {
                            $buckets[$bucket['key']] = $bucket['doc_count'];
                        }
                    }
                }
            }
        }

        foreach ($items['aggregations']['all_options'] as $item) {
            if (is_array($item)) {
                foreach ($item as $options) {
                    if (isset($options['buckets'])) {
                        foreach ($options['buckets'] as $bucket) {
                            $bucketsFilter[$bucket['key']] = $bucket['key'];
                        }
                    }
                }
            }
        }

        $countProducts = $this->filter->getFilterShow() ?? 15;

        $order = $this->filter->getFilterSort() == 'default'
            ? '`product_status_id` asc'
            : '`product_status_id` asc, ' . $this->filter->getOrderField() . ' ' . $this->filter->getOrderDirect();

        $cacheKey = 'products_' . md5(json_encode($ids)) . '_' . $countProducts . '_' . md5($this->showAll). md5($order) . md5(serialize($_GET));

        $products = Cache::tags(['products', 'characteristic'])
            ->remember($cacheKey, 86400, function () use ($ids, $order, $countProducts) {
                $query = Product::orderByRaw($order)
                    ->cardFields()
                    ->whereIn('id', $ids)
                    //->rememberForever()
                    ->notNullPrice();
                    //->fastPaginate($countProducts);

                if ($this->showAll) {
                    return $query->get();
                }

                return $query->fastPaginate($countProducts);
            }
        );

        return [
            'products' => $products,
            'options' => $buckets,
            'optionsStart' => $bucketsFilter,
            'price' => [
                'max' => $this->convertCurrency($items['aggregations']['price_stats']['max']),
                'min' => $this->convertCurrency($items['aggregations']['price_stats']['min']),
            ],
        ];
    }

    private function convertCurrency(?int $price): ?int
    {
        if (setting('konvertirovat-cenu')) {
            return round(setting('kurs') * $price);
        }

        return $price;
    }

    private function convertCurrencyForElastic(?int $price): ?int
    {
        if (setting('konvertirovat-cenu')) {
            return round($price / setting('kurs'));
        }

        return $price;
    }
}

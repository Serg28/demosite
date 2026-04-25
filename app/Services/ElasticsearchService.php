<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Services\Filters\Filter;
use Illuminate\Support\Collection;
use stdClass;

class ElasticsearchService
{
    private Filter $filter;
    private Collection $characteristics;
    private string $options = 'options.';
    private $page;

    public function filter(Category $page, Filter $filter, Collection $allowedCharacteristics = null): array
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->characteristics = $allowedCharacteristics ? $allowedCharacteristics->pluck('id'): $page->characteristicsForFilter()->pluck('id');

        return $this->buildCollectionAfterFilter(
            $this->filterOnElasticsearch($page, $filter)
        );
    }

    private function isSales()
    {
        return $this->page->id == setting('id-kategorii-akcii');
    }

    private function isBestOffers()
    {
        return $this->page->id == setting('blok-luchshie-predlozheniya-id');
    }

    private function filterOnElasticsearch(Category $page, Filter $filter): array
    {
        $productModel = new Product();
        $selectFields = $productModel->cardFields ?? null;
        $selectedFilters = $filter->getSelectedFilters();
        $categories = (isset($selectedFilters['category'])) ? array_values($selectedFilters['category']) : null; //

        $queryElastic = $productModel::search("*")
            ->with('category') //Подгружаем категории
            ->whereIn("is_active", [1]);
            //->where("category", $page->id);

        //Если не акция, ищем в пределах категории. Иначе - по всем товарам по полю is_sale
        if (!$this->isSales()) {
            $queryElastic->where("category", $page->id);
        } else {
            $queryElastic->where("is_sale",'=', 1);
        }

        //Фильтрация по выбранной категории из урл
        if(!empty($categories)) {
            $queryElastic->whereIn("category", $categories);
        }

        //Характеристики и опции из фильтра
        $filtersIds = $filter->getSelectedFilterIds();
        foreach ($filtersIds as $idCharacteristic => $options) {
            $queryElastic->whereIn($this->options . $idCharacteristic, $options);
        }

        //Фильтр по цене
        //TODO: формирование произвольных интервалов фильтров + app/Services/Filters/AbstractFilter.php + с учетом агрегаций
        if ($filter->minPrice() && $filter->maxPrice()) {
            $queryElastic->whereBetween('price', [
                $this->convertCurrencyForElastic($filter->minPrice()),
                $this->convertCurrencyForElastic($filter->maxPrice())
            ]);
        }
        //Получаем количество результатов
        $count = $queryElastic->count();
        //Возвращаем
        $queryElastic->aggregate($this->prepareAggregations($page, $filter));
        $aggregations = $queryElastic->aggregations();

        $aggregations['price_stats'] = $aggregations['unfiltered_price_stats']['filtered']['price_stats'];

        //Выбираем только необходимые поля товара
        if ($selectFields) {
            $queryElastic->select($selectFields);
        }

        $countProducts = $this->filter->getFilterShow() ?? 18;
        //$order = $this->filter->getFilterSort() === 'default'
        //    ? '`product_status_id` asc'
        //    : '`product_status_id` asc, ' . $this->filter->getOrderField() . ' ' . $this->filter->getOrderDirect();
        $queryElastic->orderBy($this->filter->getOrderField(), $this->filter->getOrderDirect());
        //Возвращаем результат поиска в виде моделей с пагинацией
        $products = $queryElastic->paginate($countProducts);

        return [
            'count' => $count ?? 0,
            'products' => $products,
            'aggregations' => $aggregations
        ];
    }

    private function prepareAggregations(Category $page, Filter $filter): array
    {
        $aggregations = [
            'all_products' => [
                'global' => new stdClass(),
                'aggregations' => $this->prepareCharacteristicAggregations($page, $filter, true) ?: new stdClass(),
            ],
            'all_options' => [
                'global' => new stdClass(),
                'aggregations' => $this->prepareCharacteristicAggregations($page, $filter, false) ?: new stdClass(),
            ],
            'max_price' => ['max' => ['field' => 'price']],
            'min_price' => ['min' => ['field' => 'price']],
            'unfiltered_price_stats' => [
                'global' => new stdClass(),
                'aggregations' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must' => $this->prepareQuery($page, $filter, false),
                            ],
                        ],
                        'aggregations' => [
                            'price_stats' => ['stats' => ['field' => 'price']],
                        ],
                    ],
                ],
            ],
        ];

        // Агрегация для подсчета товаров по категориям всегда (без примененных фильтров)
        // Если категория - НЕ акции
        if($this->isSales() || $this->isBestOffers()) {
            $aggregations['categories'] = [
                'global' => new stdClass(),
                'aggregations' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['is_active' => 1]], // Только активные товары
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'categories' => [
                                'terms' => [
                                    'field' => 'category',
                                    'size' => 20000, // Увеличьте размер, если у вас больше 100 категорий
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // Проверка, является ли категория Акции
            // Если НЕ акции, ищем в рамках категории
            // Если акции, ищем по всем товарам по полю is_sale
            $aggregations['categories']['aggregations']['filtered']['filter']['bool']['must'][] = $this->isSales()
                ? ['term' => ['is_sale' => 1]] // Фильтр для товаров только в рамках текущей категории
                : ['term' => ['category' => $page->id]]; // Фильтр для товаров в рамках дочерних категорий текущей страницы
        }

        // Добавление агрегации для бренда, если он выбран в фильтре
        /*if ($filter->isBrandCharacteristicSelected()) {
            $aggregations['filtered_categories'] = [
                'global' => new stdClass(),
                'aggregations' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['is_active' => 1]], // Активные товары
                                    [
                                        'terms' => [
                                            $this->options . $filter->getBrandCharacteristicId() => $filter->getSelectedBrandValues(),
                                        ],
                                    ], // Фильтр по выбранным брендам
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'categories' => [
                                'terms' => [
                                    'field' => 'category',
                                    'size' => 20000,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // Проверка, является ли категория Акции
            // Если НЕ акции, ищем в рамках категории
            // Если акции, ищем по всем товарам по полю is_sale
            $aggregations['filtered_categories']['aggregations']['filtered']['filter']['bool']['must'][] = $this->isSales()
                ? ['term' => ['is_sale' => 1]] // Фильтр для товаров только в рамках текущей категории
                : ['term' => ['category' => $page->id]]; // Фильтр для товаров в рамках дочерних категорий текущей страницы

        }*/

        if (!$this->isSales()) {
            $aggregations['popular_brands'] = [
                'global' => new stdClass(),
                'aggregations' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['category' => $page->id]],  // Фильтрация по текущей категории
                                    ['term' => ['is_active' => 1]],         // Только активные товары
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'brands' => [
                                'terms' => [
                                    'field' => $this->options . $filter->getBrandCharacteristicId(),
                                    // Поле, представляющее бренд
                                    'size' => 7,
                                    // Количество популярных брендов, которые вы хотите получить
                                    'order' => ['_count' => 'desc'],
                                    // Сортировка по убыванию, чтобы получить наиболее популярные бренды
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $aggregations;
    }

    private function prepareQuery(Category $page, Filter $filter, $withPrices = true): array
    {
        $filtersIds = $filter->getSelectedFilterIds();

        $must = [
            //['terms' => ['category' => [$page->id]]],
            ['terms' => ['is_active' => [1]]],
        ];

        // Проверка, является ли категория Акции
        // Если НЕ акции, ищем в рамках категории
        // Если акции, ищем по всем товарам по полю is_sale
        if (!$this->isSales()) {
            // Фильтр для товаров в рамках текущей категории
            $must[] = ['term' => ['category' => $page->id]];
        } else {
            // Фильтр для товаров, участвующих в распродаже
            $must[] = ['term' => ['is_sale' => 1]];
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
            $must[] = ['terms' => [$this->options . $category => $options]];
        }

        return $must;
    }

    private function prepareCharacteristicAggregations(Category $page, Filter $filter, $withFilters): array
    {
        $aggregations = [];

        foreach ($this->characteristics as $id) {
            $characteristic = $this->options . $id;

            //$must = $withFilters ? $this->prepareFilter($page, $id, $filter->getSelectedFilterIds(), $filter) : [
            //    ['term' => ['category' => $page->id]],
            //    ['term' => ['is_active' => 1]],
            //];

            $must = $withFilters
                ? $this->prepareFilter($page, $id, $filter->getSelectedFilterIds(), $filter)
                : [
                    $this->isSales()
                        ? ['term' => ['is_sale' => 1]] // Если категория акции, фильтр по полю is_sale
                        : ['term' => ['category' => $page->id]], // Иначе фильтр по категории
                    ['term' => ['is_active' => 1]], // Активные товары в обоих случаях
                ];

            $aggregations[$characteristic] = [
                'filter' => ['bool' => ['must' => $must]],
                'aggregations' => [
                    $characteristic => [
                        'terms' => [
                            'field' => $characteristic,
                            'size' => 5000,
                        ],
                    ],
                ],
            ];
        }

        return $aggregations;
    }

    private function prepareFilter(Category $page, int $id, array $filtersIds, Filter $filter): array
    {
        $must = [
            //['term' => ['category' => $page->id]],
            ['term' => ['is_active' => 1]],
        ];

        // Проверка, является ли категория Акции
        // Если НЕ акции, ищем в рамках категории
        // Если акции, ищем по всем товарам по полю is_sale
        if (!$this->isSales()) {
            // Фильтр для товаров в рамках текущей категории
            $must[] = ['term' => ['category' => $page->id]];
        } else {
            // Фильтр для товаров, участвующих в распродаже
            $must[] = ['term' => ['is_sale' => 1]];
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

        foreach ($filtersIds as $idCharacteristic => $options) {
            if ($idCharacteristic !== $id) {
                $must[] = ['terms' => [$this->options . $idCharacteristic => $options]];
            }
        }
        return $must;
    }

    //Постобработка ответа на запрос к Elasticsearch
    private function buildCollectionAfterFilter(array $items): array
    {
        $products = $items['products'];
        $aggregations = $items['aggregations'];
        $count = $items['count'];

        $buckets = $this->extractBucketsProducts($aggregations['all_products']);
        $bucketsFilter = $this->extractBucketsOptions($aggregations['all_options']);
        $bucketsCategories = $this->extractBucketsFilteredCategories($aggregations);
        $bucketsBrands = $this->extractBucketsOptions($aggregations['popular_brands'] ?? []);

        return [
            'count' => $count ?? 0,
            'products' => $products,
            'categories' => $bucketsCategories,
            'brands' => $bucketsBrands,
            'options' => $buckets,
            'optionsStart' => $bucketsFilter,
            'price' => [
                'max' => $this->convertCurrency($aggregations['price_stats']['max']),
                'min' => $this->convertCurrency($aggregations['price_stats']['min']),
            ],
        ];
    }

    private function extractBucketsProducts(array $aggregation): array
    {
        $buckets = [];
        foreach ($aggregation as $item) {
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
        return $buckets;
    }

    private function extractBucketsOptions(array $aggregation): array
    {
        $buckets = [];
        foreach ($aggregation as $item) {
            if (is_array($item)) {
                foreach ($item as $options) {
                    if (isset($options['buckets'])) {
                        foreach ($options['buckets'] as $bucket) {
                            $buckets[$bucket['key']] = $bucket['key'];
                        }
                    }
                }
            }
        }
        return $buckets;
    }

    //Обрабатываем аггрегацию категорий.
    //Если не выбран фильтр Бренд, то учитываются все категории.
    //Если Бренд выбран, то учитываются дополнительно категории, в которых присутствуют товары с этим брендом
    //На основании этого подсчитывается количество найденных товаров в каждой категории и пустые не выводятся
    private function extractBucketsFilteredCategories(array $aggregations)
    {
        $buckets = [];

        // Проверяем наличие ключа 'filtered_categories' и 'filtered' в агрегациях
        // Обработка всех категорий, если бренд не выбран
        if (isset($aggregations['filtered_categories']['filtered']['categories'])) {
            foreach ($aggregations['filtered_categories']['filtered']['categories'] as $item) {
                if (is_array($item)) {
                    foreach ($item as $category) {
                        $buckets[$category['key']] = $category['doc_count'];
                    }
                }
            }
        }
        //Обработка категорий с учетом брендов, если он выбран в фильтре
        elseif (isset($aggregations['categories']['filtered']['categories']['buckets'])) {
            foreach ($aggregations['categories']['filtered']['categories']['buckets'] as $category) {
                $buckets[$category['key']] = $category['doc_count'];
            }
        }

        // Если buckets не пустой, обработаем его и извлечем список ключей категорий
        $categoryKeys = [];
        if (!empty($buckets)) {
            $categoryKeys = array_keys($buckets);
        }

        // Получаем категории по ключам. Если текущая категория имеет детей, берет подкатегории. Иначе - соседние категории
        $categories = $this->page->hasChildren() ? $this->page->children(): $this->page->siblingsAndSelf();
        $categories = $categories
            //Если бренд выбран, учитываем только категории с товарами этого бренда
            //->when($this->filter->isBrandCharacteristicSelected() /*&& !empty($categoryKeys)*/, function($query) use ($categoryKeys) {
            //    return $query->whereIn('id', $categoryKeys);
            //})
            //Если бренд не выбран, учитываем все категории
            //->when(!$this->filter->isBrandCharacteristicSelected() /*&& !empty($categoryKeys)*/, function($query) use ($categoryKeys) {
            //    return $query->whereIn('id', $categoryKeys);
            //})
            ->whereIn('id', $categoryKeys) //Условия выше отключены, берем только найденные категории
            //->orderBy('title', 'asc')
            ->get(['id', 'title', 'url', 'picture', 'slug'])
            ->sortBy(function($category) {
                return $category->t('title');
            });

        // Обогащаем модели категорий количеством документов из buckets
        foreach ($categories as $category) {
            $category->document_count = $buckets[$category->id] ?? 0;
        }

        // Возвращаем список моделей категорий с количеством документов
        return $categories;
    }

    //Метод возвращает только категории, у которых есть товары.
    //Принимает объект текущей категории. Если это родитель, выводятся данные по подкатегориям.
    //Если конечная категория, то соседей
    public function getNotEmptyCategories(Category $page): Collection
    {
        //Оптимизация. Получаем сразу нужные категории, чтобы сузить круг поиска только по ним
        $categories = $page->hasChildren() ? $page->children() : $page->siblings();
        $categoryIds = $categories->pluck('id')->toArray();

        // Подготовка агрегации для категорий
        $aggregations = [
            'categories' => [
                'global' => new stdClass(),
                'aggregations' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['is_active' => 1]], // Активные товары
                                    ['terms' => ['category' => $categoryIds]] //Оптимизация. Только по нужным категориям
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'categories' => [
                                'terms' => [
                                    'field' => 'category',
                                    'size' => 20000, // Увеличьте размер, если у вас больше 100 категорий
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Запрос в Elasticsearch для получения данных
        $queryElastic = Product::search("*")
            ->whereIn("is_active", [1])
            ->whereIn('category',$categoryIds) //Оптимизация. Только по нужным категориям
            ->aggregate($aggregations);

        // Извлечение агрегаций
        $results = $queryElastic->aggregations();

        // Извлечение данных по категориям из агрегаций
        $buckets = [];
        if (isset($results['categories']['filtered']['categories']['buckets'])) {
            foreach ($results['categories']['filtered']['categories']['buckets'] as $category) {
                $buckets[$category['key']] = $category['doc_count'];
            }
        }

        // Извлечение ключей категорий
        $categoryKeys = array_keys($buckets);

        // Получение категорий по ключам
        //$categories = $page->hasChildren() ? $page->children() : $page->siblings(); //Перенесли выборку в самый верх
        $categories = $categories
            ->whereIn('id', $categoryKeys)
            ->get(['id', 'title', 'url', 'picture', 'slug']);

        // Обогащение моделей категорий количеством документов из buckets
        foreach ($categories as $category) {
            $category->document_count = $buckets[$category->id] ?? 0;
        }

        // Возвращение списка моделей категорий с количеством документов
        return $categories->filter(function ($category) {
            return $category->document_count > 0;
        });
    }


    private function convertCurrency(?int $price): ?int
    {
        return setting('konvertirovat-cenu') ? round(setting('kurs') * $price) : $price;
    }

    private function convertCurrencyForElastic(?int $price): ?int
    {
        return setting('konvertirovat-cenu') ? round($price / setting('kurs')) : $price;
    }
}

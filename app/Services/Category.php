<?php

namespace App\Services;

use App\Http\Resources\SeoCustomUrlCatalogResource;
use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use Illuminate\Support\Facades\Cache;

class Category
{
    protected ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    //Есть ли у категории подкатегории
    public function hasChildren(CategoryModel $page): bool
    {
        return $page->hasChildren();
        //return $page->children()->rememberForever()->cacheTags(['categories'])->exists();
    }

    //Возвращаем характеристики, доступные для вывода в фильтре для текущей категории
    //Если категория имеет подкатегории, выводим только бренд
    //Если не имеет подкатегорий (конечная с товарами), то набор доступных характеристик
    public function characteristicsForFilters(CategoryModel $page)
    {
        return $page->characteristicsForFilter(); //Хар-ки доступны только для конечных категорий.
        //return $this->hasChildren($page) ? $page->characteristicsForParents() : $page->characteristicsForFilter();
    }

    //Возвращает результирующий массив со списком найденных товаров по текущим фильтрам
    public function getElasticsearchData(CategoryModel $page): array
    {
        $filter = $page->filter()->init();
        $cacheKey = 'category_' . $page->getCacheKey() . '_results_' . md5(http_build_query($_GET)) . '_' . md5(serialize($filter));
        $results = Cache::tags(['category', 'elasticsearch'])->remember(
            $cacheKey,
            now()->addDay(),
            function () use ($page, $filter) {
                return $this->elasticsearchService->filter($page, $filter);
            }
        );
        return [
            'page' => $page,
            'filter' => $filter,
            'results' => $results
        ];
    }

    // Возвращает результирующий массив с товарами без фильтров
    // Проверяет, есть ли у категории фильтры. Если есть, возвращает массив с данными, иначе - null
    public function getDataProductsWithoutFilters(CategoryModel $page): ?array
    {
        if (!$page->characteristicsForFilter()) {
            $filter = $page->filter()->init();

            $products = $this->getProductsForCategory($page, $filter);

            return [
                'page' => $page,
                'products' => $products,
                'filter' => $filter,
            ];
        }
        return null;
    }

    //Возвращает товары с фильтром (результатом фильтрации)
    //Поскольку в шаблоне используется компонент Livewire FilterProduct, то метод возвращает только
    public function getDataProductsWithFilters(CategoryModel $category): array
    {
        // Получаем данные из Elasticsearch для указанной категории
        $data = $this->getElasticsearchData($category);

        // Получаем самую верхнюю категорию (глубина 1)
        $topCategory = $category->ancestors()->whereDepth(1)->first();

        // Получаем рубрики для верхней категории, если она существует
        $data['rubrics'] = $topCategory ? $this->getRubricsForCategory($topCategory) : null;

        return $data;
    }

    // Метод для получения ВСЕХ продуктов категории (с учетом текущей сортировки и кол-ва для показа) - с пагинацией
    private function getProductsForCategory(CategoryModel $page, $filter)
    {
        $orderDefault = '`product_status_id` asc';

        return ProductModel::inCategories($page)
            ->active()->available()->/*notNullPrice()->*/orderByRaw($orderDefault)->sortedBy($filter->getFilterSort())->paginate($filter->getFilterShow());
    }

    // Метод для получения данных о подкатегориях указанной категории
    public function getRubricsForCategory(CategoryModel $page)
    {
        return $page
            ->load(['children' => fn ($q) => $q
                ->active()
                ->defaultOrder()
                ->with(['children' => fn ($sq) => $sq
                    ->active()
                    ->defaultOrder()
                ])
            ])->children;
    }

    //Вывод страницы со списком категорий и подкатегорий
    public function getCatalogCategoriesView(CategoryModel $category)
    {
        //Вариант общего шаблона для всех уровней и категорий
        $view = $category->getTemplateCatalog();

        //Вариант с шаблоном для каждого уровня вложенности
        //$view = empty($category->depth) ? $category->getTemplate('root') : $category->getTemplate('catalog_level' . $category->depth);
        //$view = view()->exists($view) ? $view : $category->getTemplateCatalog();

        return view($view, [
            'page' => $category,
            //'rubrics' => $this->getRubricsForCategory($category)
        ]);
    }

    //Вывод страницы со списком товаров С или БЕЗ фильтра (самая нижняя категория)
    public function getCatalogProductsView(CategoryModel $category)
    {
        //Проверяем, есть ли у категории фильтры. Если нет, возвращаем товары без фильтров
        $result = $this->getDataProductsWithoutFilters($category);

        //И выводим страницу с товарами без фильтров
        if (is_array($result)) {
            return view($category->getTemplateIndexWithoutFilter(), $result);
        }

        //Иначе, если есть характеристики товаров, выводим товары с фильтром
        //$result = $this->getDataProductsWithFilters($category);
        //return $this->getViewResult(...$result);
        //Но поскольку в данном проекте используем livewire-компонент FilterProduct, то просто возвращает шаблон, в котором вызван компонент
        return view($category->getTemplateIndex(), ['page' => $category]);
    }
}


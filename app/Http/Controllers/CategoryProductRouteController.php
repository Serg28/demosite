<?php

namespace App\Http\Controllers;

//use App\ProductSearchRule;
use App\Services\Category as CategoryService;
use App\Services\Product as ProductService;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\Cookie;

/**
 * Класс CategoryProductController_new
 * @package App\Http\Controllers
 *
 * Этот контроллер отвечает за обработку запросов, связанных с маршрутами продуктов и категорий.
 */
class CategoryProductRouteController extends Controller
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * Конструктор CategoryProductRouteController
     *
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param CategoryService $categoryService
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CategoryService $categoryService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->categoryService = $categoryService;
    }

    //Для обратной совместимости
    public function index(string $url = null)
    {
        return $this->route($url);
    }

    /**
     * Проверяет маршруты для категории и продукта по данному $url.
     * Выводит либо категорию, либо товар - в зависимости от маршрута
     * Если не найдено ни одного, вызывает abort с 404.
     *
     * Используется в "коробочном" варианте с вложенными урлами категорий
     * + корень каталога не переназначен в роутах
     * + определение, какой тип контента отдавать: категории, товары без фильтра или карточка товара
     *
     * @param string|null $url
     * @return mixed
     */
    public function route(string $url = null): mixed
    {
        $slug = $url ? $this->getSlug($url) : null;

        //Если категория
        $categoryRoute = $this->routeCategory($slug);
        if ($categoryRoute !== null) {
            return $categoryRoute;
        }

        //Если товар
        $productRoute = $this->product($slug);
        if ($productRoute !== null) {
            return $productRoute;
        }

        abort(404);
    }

    /**
     * Проверяет, существует ли категория с данным $slug, и затем маршрутизирует соответствующим образом.
     * Вызывает abort с 404, если категория не найдена.
     *
     * @param string|null $slug
     * @return mixed
     */
    public function routeCategory(string $slug = null)
    {

        $slug = $slug ? $this->getSlug($slug) : null;

        if (!$slug) {
            abort(404);
        }

        $category = $this->categoryRepository->getBySlug($slug);

        //if (!$category) {
        //    abort(404);
        //}

        if (!$category) {
            return null;
        }

        return $this->categoryService->hasChildren($category)
            ? $this->categoryService->getCatalogCategoriesView($category) //категории среднего уровня (подкатегории и товары детей)
            : $this->categoryService->getCatalogProductsView($category);
    }

    /**
     * Проверяет, существует ли продукт с данным $slug и возвращает его.
     * Вызывает abort с 404, если продукт не найден.
     *
     * @param string|null $slug
     * @return mixed
     */
    public function product(string $slug = null)
    {
        if (!$slug) {
            abort(404);
        }

        $product = $this->productRepository->getBySlug($slug);

        if (!$product) {
            abort(404);
        }

        //$pageData = $this->productService->getPage($product);
        $productService = app()->makeWith(ProductService::class, ['product' => $product]);
        $pageData = $productService->getPage($product);

        return view($product->getTemplateProduct(), $pageData);
    }

    public function changeProductsView(string $view): void
    {
        Cookie::queue('products-view', $view, 5000);
    }

    /**
     * Извлекает часть slug из данного $path.
     *
     * @param string $path
     * @return string
     */
    private function getSlug(string $path): string
    {
        $collectUrl = array_reverse(explode('/', $path));
        foreach ($collectUrl as $url) {
            if (!strpos($url, '=')) { //оригинал
            //if (!str_contains($url, 'property-')) {
                return $url;
            }
        }

        return '';
    }
}

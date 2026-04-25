<?php

namespace App\Http\Controllers;

use App\Services\Category as CategoryService;
use App\Services\Product as ProductService;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class CategoryProductController extends Controller
{

    protected CategoryRepository $categoryRepository;
    protected ProductRepository $productRepository;
    protected CategoryService $categoryService;
    protected ProductService $productService;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CategoryService $categoryService,
        ProductService $productService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }

    public function index(string $url): mixed
    {

        $slug = $this->getSlugCategory($url);

        $category = $this->categoryRepository->getBySlug($slug);

        if ($category) {
            /*$categoryController = new CategoryController($this->categoryService);

            return $this->categoryService->hasChildren($category)
                ? $categoryController->catalog($category)
                : $categoryController->routeCatalog($category);*/

            return $this->categoryService->hasChildren($category)
                ? $this->categoryService->getCatalogCategoriesView($category)
                : $this->categoryService->getCatalogProductsView($category);

        }

        $product = $this->productRepository->getBySlug($slug);

        if ($product) {
            return (new ProductController($this->productService))->page($product);
        }

        abort(404);
    }

    private function getSlugCategory(string $path): string
    {
        $collectUrl = array_reverse(explode('/', $path));

        foreach ($collectUrl as $url) {
            if (!strpos($url, '=')) {
                return $url;
            }
        }

        return '';
    }
}

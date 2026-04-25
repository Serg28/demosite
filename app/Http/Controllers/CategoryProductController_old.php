<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryProductControllerOld extends Controller
{

    public function index(?string $url = ''): mixed
    {

        $slug = $this->getSlugCategory($url);

        $category = Category::slug($slug)
        ->rememberForever()->cacheTags(['categories'])
            ->active()->first();

        if ($category) {
            $categoryController = new CategoryControllerOld();
            return $category->children()->rememberForever()->cacheTags(['categories'])->count()
                ? $categoryController->catalog($category)
                : $categoryController->routeCatalog($category);
        }

        $product = Product::slug($slug)/*::with(['characteristics'])*/
            ->rememberForever()->cacheTags(['products'])
            ->active()->first();

        if ($product) {
            return (new ProductController())->page($product);
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

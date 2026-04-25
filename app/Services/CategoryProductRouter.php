<?php

namespace App\Services;

use App\Http\Controllers\CategoryControllerOld;
use App\Http\Controllers\ProductControllerOld;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

class CategoryProductRouter
{
    public function check()
    {
        $path = request()->path();
        $slug = $this->getSlugCategory($path);


        $category = Category::slug($slug)
            ->rememberForever()->cacheTags(['categories'])
            ->active()->first();

        if ($category) {
            Route::get(
                '/'.$path,
                function () use ($category) {
                    return $category->children()->rememberForever()->cacheTags(['categories'])->count()
                           ? (new CategoryControllerOld())->catalog($category)
                           : (new CategoryControllerOld())->routeCatalog($category);
                }
            );
        }

        $product = Product::slug($slug)
            ->rememberForever()->cacheTags(['products'])
            ->active()->first();

        if ($product) {
            Route::get(
                '/'.$path,
                function () use ($product) {
                    return (new ProductControllerOld())->page($product);
                }
            );
        }
    }

    private function getSlugCategory($path)
    {
        $collectUrl = array_reverse(explode('/', $path));

        foreach ($collectUrl as $url) {
            if (! strpos($url, '=')) {
                return $url;
            }
        }
    }
}

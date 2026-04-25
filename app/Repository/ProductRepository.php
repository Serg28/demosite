<?php
namespace App\Repository;

use App\Models\Product;

class ProductRepository {

    public function getBySlug(string $slug): ?Product
    {
        return Product::slug($slug)->rememberForever()->cacheTags(['products'])->active()->first();
    }
}
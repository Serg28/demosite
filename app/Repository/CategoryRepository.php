<?php

namespace App\Repository;

use App\Models\Category;

class CategoryRepository
{
    public function getBySlug(string $slug): ?Category
    {
        return Category::slug($slug)->rememberForever()->cacheTags(['categories'])->active()->first();
    }
}


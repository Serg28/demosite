<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

final class SameProductsCategoryRule implements Rule
{
    /**
     * @param $attribute
     * @param $value
     */
    public function passes($attribute, $value): bool
    {
        $categoriesCount = Product::find(\explode(',', $value))->pluck('category_id')->unique()->count();

        return $categoriesCount <= 1;
    }

    public function message(): string
    {
        return __t('Товары разных категорий');
    }
}

<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

enum FeedTypeEnum: string
{
    use ToArrayTrait;

    case all_products = 'Все продукты';
    case categories = 'Категории';
}

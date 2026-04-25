<?php

namespace App\Services\Filters;

use App\Interfaces\SortInterface;
use App\Models\Category;
use App\Services\Sort\FilterSorting;

class Filter_orig extends AbstractFilter
{
    public function __construct(Category $category, ?SortInterface $sortService = null)
    {

        $this->category = $category;
        $this->sortService = $sortService ?: new FilterSorting();
        parent::__construct($this->category, $this->sortService);
    }

    public function withoutPrice(): string
    {
        return $this->category->getUrl() . $this->createUrl($this->getFilter());
    }

    public function getCategoryUrl(): string
    {
        return $this->category->getUrl();
    }

    //Формируем строку ЦЕНА, СОРТИРОВКА, КОЛИЧЕСТВО ЗАПИСЕЙ
    private function urlWithParam(string $sorting = null, int $countShow = null): string
    {
        $params = [];

        if ($this->createUrlPrice()) {
            $params[] = $this->createUrlPrice();
        }

        $params = array_merge($params, $this->sortService->urlParams($sorting, $countShow));
        return count($params) ? '/' . implode('/', $params) : '';
    }
}

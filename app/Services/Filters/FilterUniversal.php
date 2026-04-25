<?php

namespace App\Services\Filters;

use App\Interfaces\SortInterface;
use App\Services\Sort\FilterSorting;
use Illuminate\Database\Eloquent\Model;

class FilterUniversal extends AbstractFilter
{
    protected Model $model;

    public function __construct(Model $model, ?SortInterface $sortService = null)
    {
        $this->model = $model;
        $sortService = $sortService ?: new FilterSorting();
        parent::__construct($model, $sortService);
    }

    public function withoutPrice(): string
    {
        return $this->model->getUrl() . $this->createUrl($this->getFilter());
    }

    public function getCategoryUrl(): string
    {
        return $this->model->getUrl();
    }
}

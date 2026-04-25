<?php

namespace App\Services\Filters;

use App\Interfaces\SortInterface;
use App\Models\Category;
use App\Services\Sort\FilterSorting;

class Filter extends AbstractFilter
{
    private int $brandCharacteristicId; //Id характеристики Бренд (совместимость по бренду)

    public function __construct(Category $category, ?SortInterface $sortService = null)
    {
        $this->category = $category;
        $this->sortService = $sortService ?: new FilterSorting();
        $this->brandCharacteristicId = setting('characteristic-brand-id') ?? 638;
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

    public function getBrandCharacteristicId(): int
    {
        return $this->brandCharacteristicId;
    }

    public function isBrandCharacteristicSelected(): bool
    {
        return isset($this->getSelectedFilterIds()[$this->brandCharacteristicId]);
    }

    public function getSelectedBrandValues(): array
    {
        return $this->getSelectedFilterIds()[$this->brandCharacteristicId] ?? [];
    }

    public function getSelectedBrandUrlFilter(): string
    {
        return $this->createUrlForSpecificCharacteristic($this->brandCharacteristicId);
    }

    public function getUrlShowCount(int $count): string
    {
        return $this->getCategoryUrl() . $this->createUrl($this->getFilter()) . $this->urlWithParam(null, $count);
    }

    public function getUrlSort(string $sorting): string
    {
        return $this->getCategoryUrl() . $this->createUrl($this->getFilter()) . $this->urlWithParam($sorting, null);
    }

    //Формируем строку ЦЕНА, СОРТИРОВКА, КОЛИЧЕСТВО ЗАПИСЕЙ
    /*private function urlWithParam(string $sorting = null, int $countShow = null): string
    {
        $params = [];

        if ($this->createUrlPrice()) {
            $params[] = $this->createUrlPrice();
        }

        $params = array_merge($params, $this->sortService->urlParams($sorting, $countShow));
        return count($params) ? '/' . implode('/', $params) : '';
    }*/

    //Формируем строку ЦЕНА, СОРТИРОВКА, КОЛИЧЕСТВО ЗАПИСЕЙ - в виде GET-параметров для URL
    private function urlWithParam(string $sorting = null, int $countShow = null): string
    {
        // Инициализируем массив для новых параметров
        $urlParam = [];

        // Добавляем параметр цены, если он есть
        $priceParam = $this->createUrlPrice();
        if ($priceParam) {
            $urlParam[] = $priceParam;
        }

        // Получаем параметры сортировки и количества записей
        $sortParams = $this->sortService->urlParams($sorting, $countShow);

        // Объединяем новые параметры в строку
        $combinedParams = implode('&', array_merge($urlParam, $sortParams));

        // Получаем текущие GET-параметры из URL, избегая дублирования
        $currentQuery = request()->query();
        unset($currentQuery['sort'], $currentQuery['show']);

        // Парсим строку параметров в массив
        parse_str($combinedParams, $combinedParamsArray);

        // Объединяем текущие параметры с новыми, новые параметры имеют приоритет
        $finalParams = array_merge($currentQuery, $combinedParamsArray);

        // Формируем строку GET-параметров
        $queryString = http_build_query($finalParams);

        // Проверяем, есть ли уже параметры в текущем URL
        //$existingQuery = str_contains($this->getRequestPath(), '?');

        // Возвращаем URL с GET-параметрами
        return !empty($queryString) ? '/?' . $queryString : '';
    }
}

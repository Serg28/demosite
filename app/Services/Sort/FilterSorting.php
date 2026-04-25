<?php

namespace App\Services\Sort;

use App\Enums\ShowCountEnum;
use App\Enums\SortOrderEnum;
use App\Interfaces\SortInterface;

class FilterSorting implements SortInterface
{
    public string $sorting;
    public string $defaultSorting;
    public int $defaultShow;
    public int $countShow;

    public function __construct(int $defaultShow = null, string $defaultSorting = '')
    {
        $this->defaultSorting = $defaultSorting ?: SortOrderEnum::popularity();
        $this->defaultShow = $defaultShow ?: ShowCountEnum::show18();
        $this->countShow = $this->defaultShow;
        $this->sorting= $this->defaultSorting;
    }

    public function getCountAll(): array
    {
        return [
            ShowCountEnum::show12(),
            ShowCountEnum::show18(),
            ShowCountEnum::show32(),
            ShowCountEnum::show48(),
            ShowCountEnum::show62()
        ];
    }

    public function getSortingAll(): array
    {
        return [
            //SortOrderEnum::DEFAULT->value => 'За замовченням',
            //SortOrderEnum::DATE->value => 'За новизною',
            SortOrderEnum::popularity() => 'За популярністю',
            SortOrderEnum::priceUp() => 'Від дешевого до дорогого',
            SortOrderEnum::priceDown() => 'Від дорогого до дешевого',
        ];
    }

    public function getOrderField(): string
    {
        $fieldMap = [
            SortOrderEnum::priceDown() => 'price',
            SortOrderEnum::priceUp() => 'price',
            SortOrderEnum::default() => 'created_at',
            SortOrderEnum::date() => 'created_at',
            SortOrderEnum::popularity() => 'count_views',
        ];

        $this->sorting = $this->getFilterSort();

        //return $fieldMap[$this->sorting] ?? $this->sorting;
        return $fieldMap[$this->sorting] ?? $fieldMap[$this->defaultSorting] ?? '';
    }

    public function getOrderDirect(): string
    {
        $directMap = [
            SortOrderEnum::priceDown() => 'desc',
            SortOrderEnum::date() => 'desc',
            SortOrderEnum::popularity() => 'desc',
        ];

        $this->sorting = $this->getFilterSort();

        return $directMap[$this->sorting] ?? 'asc';
    }

    public function checkSelected(?string $sort, string $value): bool
    {
        // TODO: Implement checkSelected() method.
    }

    public function getSorting(): string
    {
        return $this->sorting;
    }

    public function setSorting($sorting = null): void
    {
        $this->sorting = $sorting;
    }

    public function getCurrentSortingText(): string
    {
        $this->sorting = $this->getFilterSort();

        return $this->getSortingAll()[$this->sorting] ?? $this->getSortingAll()[$this->defaultSorting] ?? '';
    }

    public function getCountShow(): int
    {
        return $this->countShow;
    }

    public function setCountShow(string $countShow = null): void
    {
        $this->countShow = $countShow;
    }

    public function getDefaultShow(): int
    {
        return $this->defaultShow;
    }

    public function setDefaultShow(string $defaultShow = null): void
    {
        $this->defaultShow = $defaultShow;
    }

    //Массив параметров для урл с сортировкой и кол-вом - в зависимости от условий
    public function urlParams(string $sorting = null, int $countShow = null): array
    {
        $params = [];

        $countShow = $countShow ?: $this->getCountShow();
        $sorting = $sorting ?: $this->getSorting();

        if ($sorting && ($sorting !== $this->defaultSorting)) {
            $params[] = 'sort=' . $sorting;
        }

        if ($countShow && $countShow !== $this->getDefaultShow()) {
            $params[] = 'show=' . $countShow;
        }

        return $params;
    }

    //Возвращает сформированный URL с переданным значением сортировки + уже имеющиеся в URL параметры GET
    /*public function getUrlSort($field): string
    {
        // Получаем текущие GET-параметры
        $queryParams = request()->query();

        // Получаем параметры сортировки через метод urlParams
        $urlParams = $this->urlParams($field);

        // Объединяем параметры сортировки с уже имеющимися GET-параметрами
        foreach ($urlParams as $param) {
            // Разделяем строку на ключ и значение
            [$key, $value] = explode('=', $param);
            $queryParams[$key] = $value;
        }

        // Формируем строку запроса и возвращаем полный URL
        //return url()->current() . '?' . http_build_query($queryParams);
        return trim(currentUrl(true) . '?' . http_build_query($queryParams), '?');
    }*/

    public function getUrlSort($field): string
    {
        // Получаем текущие GET-параметры
        $queryParams = request()->query();
        unset($queryParams['sort']);

        // Получаем параметры сортировки через метод urlParams
        $urlParams = $this->urlParams($field);

        // Объединяем параметры сортировки с уже имеющимися GET-параметрами
        foreach ($urlParams as $param) {
            // Разделяем строку на ключ и значение
            [$key, $value] = explode('=', $param);
            $queryParams[$key] = $value;
        }

        // Формируем строку запроса
        $queryString = http_build_query($queryParams);

        // Возвращаем полный URL, добавляя ? только если есть GET-параметры
        return $queryString ? currentUrl(true) . '?' . $queryString : currentUrl(true);
    }

    //Возвращает текущее значение сортировки
    public function getFilterSort()
    {
        //$sorting = request()->query('sort', '');
        $sorting = getQueryParam('sort') ?? '';

        $this->setSorting($sorting);

        return $sorting;
    }
}

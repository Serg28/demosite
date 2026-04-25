<?php

namespace App\Services\Sort;

use App\Enums\ShowCountEnum;
use App\Enums\SortOrderEnum;
use App\Interfaces\SortInterface;

class Sorting implements SortInterface
{
    public const VALUES = [
        'default' => 'За замовчуванням',
        //'date' => 'За новизною',
        'popularity' => 'За популярністю',
        'priceup' => 'Від дешевого до дорогого',
        'pricedown' => 'Від дорогого до дешевого',
    ];

    public const VALUES_SHOW = [20, 32, 48, 62];

    //--
    public int $countShow = 20;

    //private string $sorting = 'default';
    public string $sorting = 'priceup';
    //--



    private int $defaultShow;


    public function __construct()
    {
        $this->sorting = SortOrderEnum::default();
        $this->defaultShow = ShowCountEnum::show20();
        $this->countShow = $this->defaultShow;
    }


    public function getCountAll(): array
    {
        return self::VALUES_SHOW;
    }

    public function getSortingAll(): array
    {
        return self::VALUES;
    }

    public function getUrl(string $value): string
    {
        $params = request()->all();
        $params['sort'] = $value;

        return url()->current().'?'.http_build_query($params);
    }

    public function checkSelected(?string $sort, string $value): bool
    {
        return $sort === $value;
    }

    //--
    public function urlWithParam(string $sorting = null, string $countShow = null): string
    {
        $params = request()->all();

        $countShow = $countShow ?: $this->countShow;
        $sorting = $sorting ?: $this->sorting;

        if ($sorting && $sorting !== 'default') {
            $params['sort'] = $sorting;
            //$params[] = 'sort='.$sorting;
        } else {
            unset($params['sort']);
        }

        if ($countShow && $countShow != 20) {
            $params['show'] = $countShow;
            //$params[] = 'show='.$countShow;
        } else {
            unset($params['show']);
        }

        return count($params) ? url()->current().'?'.http_build_query($params) : url()->current();
        //return count($params) ? url()->current() .'/' . implode('/', $params) : url()->current();
    }
    //--
    public function getSorting(): string
    {
        return $this->sorting;
    }

    public function setSorting($sorting = null): void
    {
        $this->sorting = $sorting;
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

    public function urlParams(string $sorting = null, int $countShow = null): array
    {
        // TODO: Implement urlParams() method.
    }
}

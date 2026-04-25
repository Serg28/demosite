<?php

namespace App\Services;

class Sorting
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
    public string $sorting = 'default';
    //--

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
        //return url()->current().'?'.http_build_query($params);
        //return count($params) ? url()->current() .'/' . implode('/', $params) : url()->current();
    }
    //--
}

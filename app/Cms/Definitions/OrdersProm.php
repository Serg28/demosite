<?php

namespace App\Cms\Definitions;

use App\Cms\Services\ActionsOrders;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Fields\Id;
use Vis\Builder\Services\Export;

class OrdersProm extends Orders
{
    public $title = 'Prom заказы';

    public function getFilterScope($collection)
    {
        //return $collection->where('prom_id', '!=', null);
        return $collection->where(DB::raw('COALESCE(prom_id,0)'), '!=', 0); // с корректным null
    }

    public function buttons(): array
    {
        return [
            Export::class,
        ];
    }

    //Добавить действие для откр. в новом окне
    public function actions()
    {
        return ActionsOrders::make()->orderopen()->insert()->update()->revisions()->delete();
    }
}

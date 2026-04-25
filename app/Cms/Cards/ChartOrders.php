<?php

namespace App\Cms\Cards;

use App\Models\Order;
use Vis\Builder\Services\Trend;

class ChartOrders extends Trend
{
    public $title = 'Кол. заказов';

    public function calculate()
    {
        return $this->countByDays(Order::class, 'id');
    }
}

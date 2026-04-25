<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function getOrderPriceByDate(): string
    {
        $timeStart = request('from').' 00:00:00';
        $timeEnd = request('to').' 23:59:59';
        $method = request('method');
        $sum = Order::whereBetween('created_at', [$timeStart, $timeEnd])->$method('cost');

        return number_format(round($sum)).' грн.';
    }
}

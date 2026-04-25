<?php

namespace App\Cms\Definitions;

use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Models\StatisticSearch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Definitions\Resource;

class Dashboard extends Resource
{
    public $title = 'Рабочий стол';

    public function getList()
    {
        $lastUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $lastSearchQuery = StatisticSearch::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
        $lastOrder = Order::orderBy('created_at', 'desc')->limit(5)->get();
        $topSearch = StatisticSearch::select(['*',  DB::raw('COUNT(id) as count_search')])
            ->groupBy('query')->orderBy('count_search', 'desc')->limit(5)->get();
        $lastComments = Comment::orderBy('created_at', 'desc')->limit(5)->get();

        $totalCost = Order::sum('cost');
        $avgCost = Order::avg('cost');
        $costToday = Order::whereDate('created_at', Carbon::today())->sum('cost');

        $views = Product::orderBy('count_views', 'desc')->limit(5)->get();

        $orders = OrderProducts::select('*', DB::raw('SUM(count) as count_order'))
            ->groupBy('product_id')
            ->orderBy('count_order', 'desc')
            ->with('product')->has('product')
            ->limit(5)->get();

        $from = Order::first();
        $to = Order::latest()->first();

        return view(
            'cms.dashboard',
            compact(
                'lastUsers',
                'lastSearchQuery',
                'lastOrder',
                'topSearch',
                'lastComments',
                'totalCost',
                'avgCost',
                'costToday',
                'views',
                'orders',
                'from',
                'to'
            )
        );
    }
}

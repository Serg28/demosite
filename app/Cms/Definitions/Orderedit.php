<?php

namespace App\Cms\Definitions;

use App\Models\Order;
use Illuminate\Support\Facades\Session;
use Vis\Builder\Definitions\Resource;

class Orderedit extends Resource
{
    public $model = Order::class;

    public $title = 'Редактирование заказа';

    public function getList()
    {
        if (request()->get('id')) {
            header('Location: /admin/orderedit/?o='.request()->get('id'));
        }
        if (request()->get('o')) {
            Session::put('orderId', request()->get('o'));
            $orderId = Session::get('orderId');
        } else {
            $orderId = Session::get('orderId');
            ($orderId) ?
                header('Location: /admin/orderedit/?o='.$orderId) :
                header('Location: /admin/orders/');
        }

        $order = ($orderId) ? $this->model()->where('id', '=', $orderId)->first() : collect();
        $hasOrder = $order?->id ?? '';
        if (empty($hasOrder)) {
            session()->forget('orderId');
            header('Location: /admin/orders/');
            exit;
        }


        $data = $this;
        $parent_title = __cms('Заказы');
        $parent_url = '/admin/orders';

        $orderNum = $order->order_number ?? $order->id;
        //$orderOld = $order->id_old ?? false;
        $orderOld = $order->id_old !== null && $order->id_old !== false && $order->id_old !== 0 ? $order->id_old : false;

        $is_prom = ($order) ? (@$order->prom_id ?? false) : false;
        $is_quick = ($order) ? (@$order->is_quick ?? false) : false;

        /*if ($is_prom) {
            $parent_title = __cms("Prom заказы");
            $parent_url = '/admin/orders_prom';
        } elseif ($is_quick) {
            $parent_title = __cms("Быстрые заказы");
            $parent_url = '/admin/orders_quick';
        }*/

        return view(
            'cms.definations.order',
            compact('data', 'orderId', 'orderNum', 'orderOld', 'is_prom', 'is_quick', 'parent_title', 'parent_url', 'order')
        );
    }

    public function fields()
    {
        return [];
    }
}

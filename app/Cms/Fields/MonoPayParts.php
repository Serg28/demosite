<?php

namespace App\Cms\Fields;

use App\Models\Order;
use Vis\Builder\Fields\Virtual;

class MonoPayParts extends Virtual
{
    public function getFieldForm($definition)
    {
        if ($this->getAllData()) {
            $id = request()->all()['id'];
            $order = Order::where('id', $id)->first();

            /*
            if (isset($order->mono_payparts_info)) {
                if (@$order->mono_payparts_info['state'] && @$order->mono_payparts_info['order_sub_state']) {
                    $status = __cms("monopayparts_" . $order->mono_payparts_info['state'] . "_" . $order->mono_payparts_info['order_sub_state']);
                } elseif (@$order->mono_payparts_info['order_id'] && (!@$order->mono_payparts_info['state'] && !@$order->mono_payparts_info['order_sub_state'])) {
                    $status = __cms("Заявка отправлена в банк. Ожидаем ответ ...");
                } else {
                    $status = '';
                }
            } else {
                $status = '';
            }*/

            if (isset($order->mono_payparts_info)) {
                $state = $this->getState($order);
                $subState = $order->mono_payparts_info['order_sub_state'] ?? null;
                if ($state && $subState) {
                    $status = __cms("monopayparts_" . $state . "_" . $subState);
                } elseif (isset($order->mono_payparts_info['order_id']) && empty($state) && empty($subState)) {
                    $status = __cms("Заявка отправлена в банк. Ожидаем ответ ...");
                } else {
                    $status = '';
                }
            } else {
                $status = '';
                $subState = '';
                $state = '';
            }

            return view('cms.fields.monopayparts', compact('definition', 'id', 'order', 'status', 'state', 'subState'))->render();
        }

        return '';
    }

    private function getState($order)
    {
        return $order->mono_payparts_info['state'] ?? null;
    }
}

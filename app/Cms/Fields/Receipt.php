<?php

namespace App\Cms\Fields;

use App\Models\Order;
use App\Models\OrderReceipt;
use Vis\Builder\Fields\Virtual;

class Receipt extends Virtual
{
    public function getFieldForm($definition)
    {
        if ($this->getAllData()) {
            $id = request()->all()['id'];
            $order = Order::where('id', $id)->first();
            /*$orderReceipt = OrderReceipt::where('order_id', $id);

            $receipt = $orderReceipt->first(); //чек для заказа без предоплаты
            $receipt_prepayment = $orderReceipt->where('type', 'prepayment')->first(); //чек для предоплаты (у заказа с предоплатой)
            $receipt_main_payment = $orderReceipt->where('type', '!=', 'prepayment')->first(); //чек оставшейся суммы (у заказа с предоплатой)*/

            // получите все записи заранее
            $orderReceipts = OrderReceipt::where("order_id", $id)->get();

            // извлекаем первую запись
            $receipt = $orderReceipts->first(); //чек для заказа без предоплаты

            // теперь извлекаем записи по типу
            $receipt_prepayment = $orderReceipts->firstWhere("type", "prepayment");  //чек для предоплаты (у заказа с предоплатой)
            $receipt_main_payment = $orderReceipts->firstWhere("type", "!=", "prepayment"); //чек оставшейся суммы (у заказа с предоплатой)

            return view('cms.fields.receipt', compact('definition', 'id', 'receipt', 'receipt_prepayment', 'receipt_main_payment', 'order'))->render();
        }

        return '';
    }
}

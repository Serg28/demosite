<?php

namespace App\Services;

use App\Models\OrderReceipt;
use Illuminate\Support\Facades\Log;

class CheckoutUaService
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function checkout()
    {
        if (! OrderReceipt::where('order_id', $this->order->id)->first()) {
            Log::info('---Fiskal after paid. Finding: order '.$this->order->id);
            $checkbox = new CheckboxUa($this->order->recipient);
            $response = $checkbox->createReceipt($this->order);
            Log::info('---Fiskal after paid. Response: order '.print_r($response, 1));
            if (array_key_exists('message', $response)) {
                $checkbox_message = '. '.__cms('Фискализация чека').': '.$response['message'].' - '.__cms('Проверьте, чтобы количество и суммы не были нулевыми').'. '.__cms('Затем повторите фискализацию снова');
                $data = [
                    'success' => false,
                    'title' => __cms('Ошибка'),
                    'message' => __cms('Оплата заказа подтверждена').$checkbox_message,
                ];

                return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            $checkbox_message = '. '.__cms('Фискализация прошла успешно. Перезайдите в этот заказ, чтобы увидеть ссылки на чеки');
            $receipt = OrderReceipt::create([
                'uuid' => $response['id'],
                'order_id' => $this->order->id,
            ]);
            if ($this->order->email) {
                $checkbox->sendReceiptToEmail($receipt, $this->order);
                Log::info('---Fiskal after paid. Send Email to '.$this->order->email.' for order '.$this->order->id);
            }
        }
    }

}

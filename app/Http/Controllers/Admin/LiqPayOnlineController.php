<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Order\SendMailToUserLiqPayOnlineLinkLetterJob;
use App\Models\Order;
use App\Models\OrderReceipt;
use App\Services\CheckboxUa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LiqPayOnlineController extends Controller
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    /*
    public function holdcompletion(Order $order): JsonResponse
    {
        $response = $order->pay()->request('hold_completion');
        $response = json_decode($response, true);
        $checkbox_message = '';
        $data = [
            'success' => false,
            'title' => __cms('Ошибка'),
            'message' => __cms('Оплата заказа завершилась с ошибкой') . ': ' . $response['status'] . ((@$response['err_description']) ? ' - ' . $response['err_description'] : ''),
        ];

        if ($response['result'] == 'ok' && $response['status'] == 'success') {

            Log::info('---Start Checkbox after paid: order ' . $order->id);

            //Фискализация //TODO: вынести в сервис
            //$receipt_type = ($order->prepayment_amount) ? 'prepayment' : 'main_payment';

            $receipt_type = ($order->getPrepaymentAmount()) ? 'prepayment' : 'main_payment';

            Log::info('Checkbox after paid for order '.$order->id.' started.');

            if (!OrderReceipt::where('order_id', $order->id)->first()) {

                Log::info('---Checkbox after paid. Finding: order ' . $order->id);

                $checkbox = new CheckboxUa($order->recipient, $receipt_type);
                $response = $checkbox->createReceipt($order);

                Log::info('---Checkbox after paid. Response: order '.$order->id.': '.print_r($response, 1).', recipient: '.print_r(($order->recipient->toArray() ?? []), 1));

                if (array_key_exists('message', $response)) {

                    $checkbox_message = '. ' . __cms('Фискализация чека') . ': ' . $response['message'] . ' - ' . __cms('Также проверьте, чтобы количество и суммы не были нулевыми');

                    $data = [
                        'success' => false,
                        'title' => __cms('Ошибка'),
                        'message' => __cms('Оплата заказа подтверждена') . $checkbox_message,
                    ];

                    return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                } else {
                    $checkbox_message = '. ' . __cms('Фискализация прошла успешно. Перезайдите в этот заказ, чтобы увидеть ссылки на чеки');
                }
                $receipt = OrderReceipt::create([
                    'uuid' => $response['id'],
                    'order_id' => $order->id,
                    'type' => $receipt_type
                ]);
                if ($order->email) {
                    $checkbox->sendReceiptToEmail($receipt, $order);
                    Log::info('---Checkbox after paid. Send Email to ' . $order->email . ' for order ' . $order->id);
                }
            }
            //--

            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms('Оплата заказа подтверждена') . $checkbox_message,
            ];
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }*/

    public function holdcompletion(Order $order): JsonResponse
    {
        $response = $order->pay()->request('hold_completion');
        $response = json_decode($response, true);
        $errorDetails = data_get($response, 'err_description', '');

        $data = [
            'success' => false,
            'title' => __cms('Ошибка'),
            'message' => __cms('Оплата заказа завершилась с ошибкой') . ': ' . data_get($response, 'status', '') . ($errorDetails ? ' - ' . $errorDetails : ''),
        ];

        if ($response['result'] == 'ok' && $response['status'] == 'success') {

            Log::info('Checkbox after paid for order '.$order->id.' started.');

            $receiptType = ($order->getPrepaymentAmount()) ? 'prepayment' : 'main_payment';

            if (!OrderReceipt::where('order_id', $order->id)->first()) {

                $checkbox = new CheckboxUa($order->recipient, $receiptType);
                $result = $checkbox->createAndSendReceipt($order);

                $data = [
                    'success' => $result['success'] ?? false,
                    'title' => $result['success'] ? __cms('Успех') : __cms('Ошибка'),
                    'message' => __cms('Оплата заказа подтверждена') . ($result['message'] ?? ''),
                ];

                return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            $data = [
                'success' => true,
                'title' => __cms('Успех'),
                'message' => __cms('Оплата заказа подтверждена') . '. '.__cms('Фискализация прошла успешно. Перезайдите в этот заказ, чтобы увидеть ссылки на чеки'),
            ];

            Log::info('Checkbox after paid for order '.$order->id.' finished.');
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function refund(Order $order): JsonResponse
    {
        $response = $order->pay()->request('refund');
        $response = json_decode($response, true);
        $data = [
            'success' => false,
            'title' => __cms('Ошибка'),
            'message' => __cms('Оплата заказа завершилась с ошибкой') . ((@$response['err_description']) ? ': ' . $response['err_description'] : ''),
        ];
        if ($response['result'] == 'ok' && $response['status'] == 'reversed') {
            $order->update([
                'liqpay_order_id' => '',
            ]);
            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __t('Оплата успешно возвращена покупателю'),
            ];
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //Отправка Email со ссылкой на оплату
    public function newlink(Order $order): JsonResponse
    {
        //Создаем и сохраняем уникальный номер заказа для LiqPay
        $order->update([
            'liqpay_order_id' => $order->id . '_' . time(),
        ]);

        $data = [
            'success' => false,
            'title' => __cms('Ошибка'),
            'message' => __cms('Что-то пошло не так, попробуйте позже'),
        ];
        if (SendMailToUserLiqPayOnlineLinkLetterJob::dispatch($order)) {
            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms('На электронный адрес пользователя отправлено письмо для оплаты заказа'),
            ];
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

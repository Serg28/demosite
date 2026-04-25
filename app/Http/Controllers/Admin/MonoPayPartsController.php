<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Order\SendMailToUserLiqPayOnlineLinkLetterJob;
use App\Models\Order;
use App\Models\OrderReceipt;
use App\Services\CheckboxUa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MonoPayPartsController extends Controller
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    public function reject(Order $order): JsonResponse
    {
        $response = $order->pay()->reject();
        //$checkbox_message = '';
        if (isset($response['order_id'])) {
            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms("Заявка успешно отменена").'. '
                    .__cms("Статус заявки"). ': '
                    .__cms('monopayparts_'.@$response['state'].'_'.@$response['order_sub_state']). ' '
                    .@$response['message']
            ];
        } else {
            $data = [
                'success' => false,
                'title' => __cms('Ошибка'),
                'message' => __cms("Отмена заявки завершилась с ошибкой") . ': ' . @$response['message'],
            ];
        }

        //if ($response['result'] == 'ok' && $response['status'] == 'success') {
            /*Log::info('---Start fiskal after paid: order '.$order->id);
            //Фискализация //TODO: вынести в сервис
            if (! OrderReceipt::where('order_id', $order->id)->first()) {
                Log::info('---Fiskal after paid. Finding: order '.$order->id);
                $checkbox = new CheckboxUa($order->recipient);
                $response = $checkbox->createReceipt($order);
                Log::info('---Fiskal after paid. Response: order '.print_r($response, 1));
                if (array_key_exists('message', $response)) {
                    $checkbox_message = '. '.__cms('Фискализация чека').': '.$response['message'].' - '.__cms('Проверьте, чтобы количество и суммы не были нулевыми').'. '.__cms('Затем повторите фискализацию снова');
                    //return response()->json(['message' => $response['message']]);
                    $data = [
                        'success' => false,
                        'title' => __cms('Ошибка'),
                        'message' => __cms('Оплата заказа подтверждена').$checkbox_message,
                    ];

                    return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                } else {
                    $checkbox_message = '. '.__cms('Фискализация прошла успешно. Перезайдите в этот заказ, чтобы увидеть ссылки на чеки');
                }
                $receipt = OrderReceipt::create([
                    'uuid' => $response['id'],
                    'order_id' => $order->id,
                ]);
                if ($order->email) {
                    $checkbox->sendReceiptToEmail($receipt, $order);
                    Log::info('---Fiskal after paid. Send Email to '.$order->email.' for order '.$order->id);
                }
            }*/
            //--

            /*$data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms('Оплата заказа подтверждена').$checkbox_message,
            ];*/
        //}

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function return(Order $order): JsonResponse
    {
        $response = $order->pay()->return();
        //$checkbox_message = '';
        if (isset($response['status'])) {
            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms("Товар успешно возвращен")
            ];
        } else {
            $data = [
                'success' => false,
                'title' => __cms('Ошибка'),
                'message' => __cms("Возврат товара завершился с ошибкой") . ': ' . @$response['message'],
            ];
        }
        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function confirmation(Order $order): JsonResponse
    {
        $response = $order->pay()->confirmation();
        if (isset($response['state'], $response['order_sub_state'])) {
            $data = [
                'success' => true,
                'title' => __cms('Успешно'),
                'message' => __cms("Запрос на подтверждение выдачи товара отправлен успешно. "). __cms("monopayparts_" . $response['state'] . "_" . $response['order_sub_state'])
            ];
        } else {
            $data = [
                'success' => false,
                'title' => __cms('Ошибка'),
                'message' => __cms("Подтверждение выдачи товара завершилось с ошибкой") . ': ' . @$response['message'],
            ];
        }
        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}

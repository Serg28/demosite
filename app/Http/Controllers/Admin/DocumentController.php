<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Order\SendMailToUserOrderChangedLetterJob;
use App\Jobs\Order\SendMailToUserRequisitesLetterJob;
use App\Jobs\Order\SendSmsOrderJob;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    public function printing(Order $order): View
    {
        return view('cms.document.printing', compact('order'));
    }

    public function receipt(Order $order): View
    {
        $count = $order->products()->sum('count');

        return view('cms.document.receipt', compact('order', 'count'));
    }

    public function invoice(Order $order): View
    {
        return view('cms.document.invoice', compact('order'));
    }

    public function account(Order $order): View
    {
        return view('cms.document.account', compact('order'));
    }

    //Отправка Email с реквизитами на почту клиенту
    public function orderChangedEmail(Order $order): JsonResponse
    {
        $message = (SendMailToUserOrderChangedLetterJob::dispatch($order)) ? __cms('Сообщение успешно отправлено') : __cms('Что-то пошло не так, попробуйте позже');

        return response()->json(['message' => $message], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //Отправка Email с реквизитами на почту клиенту
    public function email(Order $order): JsonResponse
    {
        $message = (SendMailToUserRequisitesLetterJob::dispatch($order)) ? __cms('Сообщение успешно отправлено') : __cms('Что-то пошло не так, попробуйте позже');

        return response()->json(['message' => $message], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //Отправка SMS с реквизитами на почту клиенту
    public function sms(Order $order): JsonResponse
    {
        $message = (SendSmsOrderJob::dispatch($order, 'sms.order.new_requisites')) ? __cms('Сообщение успешно отправлено') : __cms('Что-то пошло не так, попробуйте позже');

        return response()->json(['message' => $message], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function order(Order $order)
    {
        return 'ok';
    }
}

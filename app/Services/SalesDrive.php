<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalesDrive
{
    private $url;

    private $formCode;

    private string $slugHandler = '/handler/';

    public function __construct()
    {
        $this->url = setting('cabinet-sales-drive');
        $this->formCode = setting('formcode-sales-drive');
    }

    public function sendOrder($data)
    {
        $data = $this->prepareOrder($data);
        Log::info('Send order to sales drive');
        Log::info('url - '.$this->url.$this->slugHandler);
        Log::info($data);

        return Log::info(Http::post($this->url.$this->slugHandler, $data));
    }

    public function sendFeedback($data)
    {
        $data = $this->prepareFeedback($data);
        Log::info('Send feedback to sales drive');
        Log::info('url - '.$this->url.$this->slugHandler);
        Log::info($data);

        return Log::info(Http::post($this->url.$this->slugHandler, $data));
    }

    private function prepareFeedback(Feedback $feedback): array
    {
        return [
            'form' => $this->formCode,
            'getResultData' => '1',
            'sajt' => env('APP_URL'),
            'fName' => $feedback->name ?? '',
            'phone' => $feedback->phone ?? '',
            'email' => $feedback->email ?? '',
            'comment' => $feedback->comment ?? '',
        ];
    }

    private function prepareOrder(Order $order): array
    {
        return [
            'form' => $this->formCode,
            'getResultData' => '1',
            'products' => isset($order->products) ? $this->formProductsList($order->products) : '',
            'fName' => $order->name ?? '',
            'phone' => $order->phone ?? '',
            'email' => $order->email ?? '',
            'shipping_method' => isset($order->delivery) ? $order->delivery->t('title') : '',
            'payment_method' => isset($order->payMethod) ? $order->payMethod->t('title') : '',
            'shipping_address' => $order->address ?? '',
            'sajt' => env('APP_URL'),
            'prodex24source_full' => isset($order->orderUtm) ? $order->orderUtm->utm_source : '',
            'prodex24source' => isset($order->orderUtm) ? $order->orderUtm->utm_source : '',
            'prodex24medium' => isset($order->orderUtm) ? $order->orderUtm->utm_medium : '',
            'prodex24campaign' => isset($order->orderUtm) ? $order->orderUtm->utm_campaign : '',
            'prodex24content' => isset($order->orderUtm) ? $order->orderUtm->utm_content : '',
            'prodex24term' => isset($order->orderUtm) ? $order->orderUtm->utm_term : '',
        ];
    }

    private function formProductsList($products)
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->product->id,
                'name' => $product->product->t('title'),
                'costPerItem' => $product->product->getPrice(),
                'amount' => $product->count,
                'description' => $product->product->t('short_description'),
                'discount' => $product->product->getPriceOld() ? $product->product->getSale() : '',
            ];
        });
    }
}

<?php

//Переопределение vendor/arturishe21/laravel-liqpay/src/LiqPay.php

namespace App\Services\Checkouts\LiqPay;

use Arturishe21\LiqPay\Support\LiqPay as AbstractLiqPay;

class LiqPayService
{
    protected AbstractLiqPay $client;

    public function __construct()
    {
        $this->client = new AbstractLiqPay(config('liqpay.public_key'), config('liqpay.private_key'));
    }

    public function pay(
        $action = 'pay',
        $amount = 1.00,
        $currency = 'UAH',
        $description = 'foo',
        $order_id = 'bar',
        $result_url = '',
        $server_url = ''
    ) {
        $form = $this->client->cnbForm([
            'action' => $action,
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'order_id' => $order_id,
            'version' => '3',
            'result_url' => $result_url,
            'server_url' => $server_url,
        ]);

        $script = '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script><script type="text/javascript">$("form").submit();</script> ';

        return $form.$script;
    }

    public function status(string $orderId = 'bar'): mixed
    {
        $data = $this->client->api('request', [
            'action' => 'status',
            'version' => '3',
            'order_id' => $orderId,
        ]);

        return json_encode($data, true);
    }

    public function request($orderId = 'bar', $amount = 0, $action = 'status'): mixed
    {
        $data = $this->client->api('request', [
            'action' => $action,
            'version' => '3',
            'order_id' => $orderId,
            'amount' => $amount,
            'public_key' => config('liqpay.public_key'),
        ]);

        return json_encode($data, true);
    }
}

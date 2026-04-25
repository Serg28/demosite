<?php

namespace App\Console\Commands;

use App\Events\OrderCreate;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\OrderStatus;
use App\Services\PromApi;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportFromProm extends Command
{
    public function __construct(private PromApi $promApi, private UserService $userService)
    {
        parent::__construct();
    }

    protected $signature = 'orders:import_from_prom';

    protected $description = 'Import order from Prom';

    public function handle(): void
    {
        Log::info('--Import prom started--');
        $response = $this->promApi->getOrder();

        if (isset($response['orders'])) {
            foreach ($response['orders'] as $order) {
                if (Order::where('prom_id', $order['id'])->doesntExist()) {
                    $user = collect([
                        'email' => $order['email'],
                        'phone' => $order['phone'],
                        'first_name' => $order['client_first_name'],
                        'last_name' => $order['client_last_name'],
                    ]);

                    $data = [
                        'prom_id' => $order['id'],
                        'user_id' => $this->userService->getUserOrCreate($user),
                        'first_name' => $order['client_first_name'],
                        'last_name' => $order['client_last_name'],
                        'email' => $order['email'],
                        'phone' => $order['phone'],
                        'comment' => $order['client_notes'],
                        'cost' => $this->clearPrice($order['full_price']),
                        'order_status_id' => $this->getStatusId($order['status']),
                        'created_at' => $order['date_created'],
                        'updated_at' => $order['date_modified'],
                        'pay_method_id' => 1,
                        'delivery_id' => $this->getDeliveryId($order['delivery_option']),
                        'is_online_payed' => 1,
                        'address' => $order['delivery_address'],
                        'price_delivery' => $order['delivery_cost'],
                    ];

                    Log::info('---prom order info--');
                    Log::info($order);
                    Log::info($data);

                    $orderCms = Order::create($data);

                    foreach ($order['products'] as $product) {
                        OrderProducts::create([
                            'order_id' => $orderCms->id,
                            'product_id' => $product['external_id'],
                            'count' => $product['quantity'],
                            'price' => $this->clearPrice($product['price']),
                            'base_price' => $this->clearPrice($product['price']),
                        ]);
                    }

                    OrderCreate::dispatch($orderCms);
                }
            }
        }
        Log::info('--Import prom finished--');
    }

    private function clearPrice(string $price): int
    {
        return filter_var($price, FILTER_SANITIZE_NUMBER_INT);
    }

    private function getStatusId(string $status): int
    {
        $status = OrderStatus::where('name_for_prom', $status)->first();

        if ($status) {
            return $status->id;
        }

        return 1;
    }

    private function getDeliveryId(array $delivery)
    {
        if (isset($delivery['id'])) {
            $delivery = Delivery::where('prom_delivery_id', $delivery['id'])->first();

            if ($delivery) {
                return $delivery->id;
            }
        }

        return 1;
    }
}

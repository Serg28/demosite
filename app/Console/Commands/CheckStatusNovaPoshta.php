<?php

namespace App\Console\Commands;

use App\Repository\OrderRepository;
use App\Services\NovaPoshtaApi2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStatusNovaPoshta extends Command
{
    private $repository;

    private $np;

    public function __construct(OrderRepository $repository, NovaPoshtaApi2 $np)
    {
        $this->repository = $repository;
        $this->np = $np;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check_np_statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking parcel statuses in Nova Poshta and changing order statuses';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('---Start CRON CheckStatusNovaPoshta---');
        $npOrders = $this->repository->getAllNovaPoshtaOrders();
        if ($npOrders->isNotEmpty()) {
            foreach ($npOrders as $order) {
                if ($order->tracking_num && $order->np_info['phone']) {
                    $fields = [];
                    $response = $this->np->documentsTracking($order->tracking_num, $order->np_info['phone']);
                    $np_status = $response['data'][0]['StatusCode'] ?? 0;
                    //$np_status = 9;
                    switch ($np_status) {
                        case 4: //Статусы, указывающие на то, что посылка принята НП
                        case 5:
                        case 6:
                        case 41:
                            $fields['order_status_id'] = 5; //Статус заказа - Отправлен
                            break;
                        case 9: //Статусы, указывающие на то, что клиент посылку забрал
                        case 10:
                            $fields['order_status_id'] = 7; //Статус заказа - Выполнен
                            break;
                        default:
                            break;
                    }
                    if (
                        count($fields) > 0 && // Если статус изменился
                        $order->order_status_id !== 7 && // Если заказ еще не выполнен
                        $fields['order_status_id'] !== $order->order_status_id // Если заказ еще не имеет этот статус
                    ) {
                        $order->update($fields);
                        $this->info('Order '.$order->id.': Status NP: '.$np_status.'; Order status CHANGED to '.$fields['order_status_id']);
                        Log::info('CRON CheckStatusNovaPoshta: Order '.$order->id.': Status NP: '.$np_status.'; Order status CHANGED to '.$fields['order_status_id']);
                    } else {
                        $this->info('Order '.$order->id.': Status NP: '.$np_status.'; Order status NOT changed');
                        Log::info('CRON CheckStatusNovaPoshta: Order '.$order->id.': Status NP: '.$np_status.'; Order status NOT changed');
                    }
                }
            }
        } else {
            $this->info('Orders list is empty');
            Log::info('CRON CheckStatusNovaPoshta: Orders list is empty');
        }
        Log::info('---Finished CRON CheckStatusNovaPoshta---');

        return Command::SUCCESS;
    }
}

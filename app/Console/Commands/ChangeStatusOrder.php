<?php

namespace App\Console\Commands;

use App\Models\Order;
use Bschmitt\Amqp\Facades\Amqp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeStatusOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:change_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command change status order';

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
        Amqp::consume('jobs_response', function ($message, $resolver): void {
            $orderStatus = json_decode($message->body);

            Log::info('--response rabbit--');
            Log::info($orderStatus);

            $order = Order::find($orderStatus->id);
            $order->order_status_id = $orderStatus->status;
            $order->save();

            $resolver->acknowledge($message);

            $resolver->stopWhenProcessed();
        });
    }
}

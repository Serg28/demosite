<?php

namespace App\Jobs\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonoPayPartsConfirmOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->onQueue('shop-emails');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job: START MONO confirmation for order '.$this->order->id);
        $response = $this->order->pay()->confirmation();
        $response = $this->order->pay()->responseToArray($response);
        Log::info('Job: MONO confirmation for order '.$this->order->id.': '.print_r($response, true));
    }
}

<?php

namespace App\Jobs\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public $fields;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $fields)
    {
        $this->order = $order;
        $this->fields = $fields;
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->fields) {
            Log::info('/*-----Start update-----*/');
            Log::info('Job: Order before update '.$this->order->id.': '.print_r($this->order->getOriginal(), true));
            $this->order->updateQuietly($this->fields);
            Log::info('Job: Order after update '.$this->order->id.': '.print_r($this->fields, true));
            Log::info('/*-----Finish update----*/');
        }
    }
}

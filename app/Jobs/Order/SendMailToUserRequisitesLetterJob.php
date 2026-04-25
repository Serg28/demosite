<?php

namespace App\Jobs\Order;

use App\Mail\OrderUserRequisitesLetter;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailToUserRequisitesLetterJob implements ShouldQueue
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
     */
    public function handle()
    {
        return Mail::to($this->order->email)->send(new OrderUserRequisitesLetter($this->order));
    }
}

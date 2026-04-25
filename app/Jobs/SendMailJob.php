<?php

namespace App\Jobs\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public $mailable;

    public $mail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $mailable)
    {
        $this->order = $order;
        $this->mailable = $mailable;
        $this->onQueue('shop-emails');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->order->email)->send($this->mailable);
    }
}

<?php

namespace App\Jobs\Order;

use App\Models\Order;
use App\Services\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public Sms $sms;

    public $template;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $template)
    {
        $this->order = $order;
        $this->sms = (new Sms());
        $this->template = $template;
        $this->onQueue('shop-sms');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = view($this->template)->with(['order' => $this->order])->render();

        // заменяем символы переноса строки на символ с кодом 0x0A
        $message = str_replace("\n", "\x0A", $message);

        // преобразуем текст в 7-битную кодировку GSM
        $message = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $message);

        $this->sms->sendText($this->order->phone, $message);
    }
}

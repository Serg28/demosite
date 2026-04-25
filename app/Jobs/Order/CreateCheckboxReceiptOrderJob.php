<?php

namespace App\Jobs\Order;

use App\Mail\CheckboxReceiptErrorAdmin;
use App\Models\Order;
use App\Services\CheckboxUa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

//Формирование чека Checkbox (полная оплата или второй чек послеоплаты)
class CreateCheckboxReceiptOrderJob implements ShouldQueue
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
        Log::info('Job: START CreateCheckboxReceiptOrderJob for order '.$this->order->id);

        if (!$this->order->orderReceipt->where('type', '=', 'main_payment')->first()) {
            $checkbox = new CheckboxUa($this->order->recipient);
            $result = $checkbox->createAndSendReceipt($this->order);

            if (isset($result['success']) && !$result['success'] && !empty(settingForMail('email-administratora'))) {
                Mail::to(settingForMail('email-administratora'))->send(new CheckboxReceiptErrorAdmin($this->order));
            }
        }


        Log::info('Job: FINISH CreateCheckboxReceiptOrderJob for order '.$this->order->id);
    }
}

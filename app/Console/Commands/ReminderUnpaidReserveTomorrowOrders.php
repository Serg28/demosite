<?php

namespace App\Console\Commands;

use App\Jobs\Order\SendSmsOrderJob;
use App\Mail\SendReminderReserveExpiredOrderLetter;
use App\Repository\OrderRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderUnpaidReserveTomorrowOrders extends Command
{
    private $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:remainder_unpaid_reserve_tomorrow_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder that the reservation period of the order will expire soon';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('---Start CRON ReminderUnpaidReserveTomorrowOrders---');
        $unpickupedOrders = $this->repository->getUnpaidReserveTomorrowOrders();
        if ($unpickupedOrders->isNotEmpty()) {
            foreach ($unpickupedOrders as $unpickupedOrder) {
                if (Mail::to($unpickupedOrder->email)->send(new SendReminderReserveExpiredOrderLetter($unpickupedOrder))) {
                    $this->info('Order '.$unpickupedOrder->id.': email remainder send to '.$unpickupedOrder->email.' successed');
                } else {
                    $this->error('Order '.$unpickupedOrder->id.': email remainder send to '.$unpickupedOrder->email.' error');
                }
                if (SendSmsOrderJob::dispatch($unpickupedOrder, 'sms.order.reserve_expires')) { //SMS о новом заказе
                    $this->info('Order '.$unpickupedOrder->id.': sms remainder send to '.$unpickupedOrder->phone.' successed');
                } else {
                    $this->error('Order '.$unpickupedOrder->id.': sms remainder send to '.$unpickupedOrder->phone.' error');
                }
            }
        } else {
            $this->info('Reminder list is empty');
        }
        Log::info('---Start CRON ReminderUnpaidReserveTomorrowOrders---');

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\Order\SendSmsOrderJob;
use App\Mail\SendReminderReserveExpiredOrderLetter;
use App\Repository\OrderRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderReserveTomorrowOrder extends Command
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
    protected $signature = 'order:remainder_reserve_tomorrow_order';

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
        Log::info('---Start CRON ReminderReserveTomorrowOrder---');
        $unpickupedOrders = $this->repository->getUnpickupedOrders();

        if ($unpickupedOrders->isEmpty()) {
            $this->info('Reminder list is empty');
            Log::info('---Finish CRON ReminderReserveTomorrowOrder---');
            return Command::SUCCESS;
        }

        foreach ($unpickupedOrders as $unpickupedOrder) {
            $this->sendReminderEmail($unpickupedOrder);
            $this->sendReminderSMS($unpickupedOrder);
        }

        Log::info('---Finish CRON ReminderReserveTomorrowOrder---');
        return Command::SUCCESS;
    }

    private function sendReminderEmail($order): void
    {
        $result = Mail::to($order->email)->send(new SendReminderReserveExpiredOrderLetter($order));
        $message = $result ? 'successed' : 'error';
        $this->logResult($order, 'email', $message);
    }

    private function sendReminderSMS($order): void
    {
        $result = SendSmsOrderJob::dispatch($order, 'sms.order.reserve_expires');
        $message = $result ? 'successed' : 'error';
        $this->logResult($order, 'sms', $message);
    }

    private function logResult($order, $channel, $message)
    {
        $this->info('Order ' . $order->id . ": $channel remainder send to $order->email $message");
    }
}

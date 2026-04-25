<?php

namespace App\Console\Commands;

use App\Mail\SendReminderReserveTodayOrderLetter;
use App\Repository\OrderRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderUnpaidReserveTodayOrder extends Command
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
    protected $signature = 'order:remainder_unpaid_reserve_today_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminding the manager of orders whose reservation expires today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('---Start CRON ReminderUnpaidReserveTodayOrder---');
        $unpickupedOrders = $this->repository->getExpiresTodayOrders();
        if ($unpickupedOrders->isNotEmpty()) {
            foreach ($unpickupedOrders as $unpickupedOrder) {
                //Админу - системеное сообщение (пока делаем письмом, потом состемное сообщение)
                //Админу - пользователю.
                if (Mail::to(settingForMail('email-administratora'))->send(new SendReminderReserveTodayOrderLetter($unpickupedOrder))) {
                    $this->info('Order '.$unpickupedOrder->id.': remainder send to '.$unpickupedOrder->email.' successed');
                } else {
                    $this->error('Order '.$unpickupedOrder->id.': remainder send to '.$unpickupedOrder->email.' error');
                }
            }
        } else {
            $this->info('Reminder list is empty');
        }
        Log::info('---Start CRON ReminderUnpaidReserveTodayOrder---');

        return Command::SUCCESS;
    }
}

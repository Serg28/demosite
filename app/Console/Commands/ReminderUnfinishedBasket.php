<?php

namespace App\Console\Commands;

use App\Mail\SendUnfinishedBusketUser;
use App\Repository\UnfinishedBasketRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReminderUnfinishedBasket extends Command
{
    private $repository;

    public function __construct(UnfinishedBasketRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:reminder_unfinished_basket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder about an unfinished basket';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unfinishedBaskets = $this->repository->getUnfinishedBaskets();

        if ($unfinishedBaskets->isNotEmpty()) {
            foreach ($unfinishedBaskets as $unfinishedBasket) {
                if (Mail::to($unfinishedBasket->user->email)->send(new SendUnfinishedBusketUser($unfinishedBasket))) {
                    $this->info('UnfinishedBasket '.$unfinishedBasket->id.': email remainder send to '.$unfinishedBasket->email.' successed');
                }
            }
        }
    }
}

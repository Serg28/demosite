<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class TestMonoOrder extends Command
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mono:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $order = Order::find(181);
        dd($order->pay()->info());
    }
}

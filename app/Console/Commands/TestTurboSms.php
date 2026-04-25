<?php

namespace App\Console\Commands;

use App\Services\Sms;
use Illuminate\Console\Command;

class TestTurboSms extends Command
{
    private Sms $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

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
        return $this->sms->sendText('+380979408386', 'Проверка
переноса
текста');

        return Command::SUCCESS;
    }
}

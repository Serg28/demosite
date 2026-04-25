<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RegenerateSettingsCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;

    public function __construct()
    {
        $this->onQueue('low');
    }

    public function handle()
    {
        //Переиндексируем и сбрасываем кеш
        Log::info('Job RegenerateSettingsCache started');
        $exitCode = Artisan::call('cache:rebuild_setting_cache');
        Log::info('Job RegenerateSettingsCache finished');
    }
}

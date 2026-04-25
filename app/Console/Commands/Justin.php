<?php

namespace App\Console\Commands;

use App\Services\JustinApi;
use Illuminate\Console\Command;

class Justin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:justin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        (new JustinApi())->parse();
    }
}

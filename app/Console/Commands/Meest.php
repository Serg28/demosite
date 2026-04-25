<?php

namespace App\Console\Commands;

use App\Services\MeestApi;
use Illuminate\Console\Command;

class Meest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:meest';

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
    public function handle()
    {
        (new MeestApi())->parse();
    }
}

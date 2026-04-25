<?php

namespace App\Console\Commands;

use App\Services\UkrposhtaApi;
use Illuminate\Console\Command;

class Ukrposhta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:ukrposhta';

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
        (new UkrposhtaApi())->parse();
    }
}

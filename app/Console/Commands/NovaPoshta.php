<?php

namespace App\Console\Commands;

use App\Services\NovaPoshtaApi;
use Illuminate\Console\Command;

class NovaPoshta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:np_cities_and_warehouse';

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
    public function handle(): void
    {
        (new NovaPoshtaApi())->parse();
    }
}

<?php

namespace App\Console\Commands;

use App\Services\Import\CsCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CsCardProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_products:cs_card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'store products data from cs_card';

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
        $url = 'https://stremyanka.com.ua/ab__pfe_1_products.xml';

        (new CsCard($url))->get();
        Artisan::call('cache:clear');
    }
}

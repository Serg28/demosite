<?php

namespace App\Console\Commands;

use App\Services\Import\SqlProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportProductsSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_products:sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from sql file';

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
        (new SqlProduct())->parse();
        Artisan::call('cache:clear');
    }
}

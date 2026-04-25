<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FixTreeCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix and rebuild product categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Category::isBroken()) {
            Category::fixTree();
            $this->info('Categories not need to rebuild!');
        } else {
            $this->info('Categories rebuild successfully!');
        }

        $this->info('Fix tree depth');
        Category::fixDepthTree();
        //$this->info('Clear cache');
        //Clear cache

        //Artisan::call('optimize:clear');
        //$this->info('create index elastic');
        //Artisan::call('search:reindex');

        return Command::SUCCESS;
    }
}

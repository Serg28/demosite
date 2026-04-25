<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearRebuildSiteCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear_rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing and rebuilding the site cache';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Clear cache');
        //Artisan::call('optimize:clear');
        Artisan::call('cache:clear');

        $this->info('Rebuilding setting cache');
        Artisan::call('cache:rebuild_setting_cache');

        $this->info('Rebuilding pages cache');
        Artisan::call('cache:rebuild_pages_cache');

        $this->info('Rebuilding event cache');
        Artisan::call('event:cache');

        $this->info('Rebuilding view cache');
        Artisan::call('view:compile');

        return Command::SUCCESS;
    }
}

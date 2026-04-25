<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearRevisionOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_revision_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear orders revision table';

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
        Log::info('--Clear orders revisions started--');
        DB::table('revisions')->where('revisionable_type', 'App\\Models\\Order')->where('created_at', '<', Carbon::now()->subYears(2))->delete();

        $this->info('Clear orders revisions for 2 years');
        Log::info('--Clear orders revisions finished--');
    }
}

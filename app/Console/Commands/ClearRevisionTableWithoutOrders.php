<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearRevisionTableWithoutOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_revision_without_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear revision table without Orders';

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
        Log::info('--Clear revisions (without orders) started--');
        DB::table('revisions')->where('revisionable_type', '!=', 'App\\Models\\Order')->where('created_at', '<', Carbon::now()->subDays(30))->delete();

        $this->info('Clear revisions for 1 month');
        Log::info('--Clear revisions (without orders) finished--');
    }
}

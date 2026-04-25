<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearRevisionTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_revision';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear revision table';

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
        DB::table('revisions')->where('created_at', '<', Carbon::now()->subDays(30))->delete();

        $this->info('Clear revisions for 1 month');
    }
}

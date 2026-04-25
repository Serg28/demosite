<?php

namespace App\Jobs;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class IncrementViewNews implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
        $this->onQueue('low');
    }

    public function handle(): void
    {
        DB::table('news')
            ->where('id', $this->news->id)
            ->increment('count_view', 1);
    }
}

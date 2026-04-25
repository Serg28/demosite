<?php

namespace App\Jobs;

use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReindexProduct implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Product $product)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $elasticsearch = app(Client::class);

        $elasticsearch->index([
            'index' => $this->product->getSearchIndex(),
            'type' => $this->product->getSearchType(),
            'id' => $this->product->getKey(),
            'body' => $this->product->toSearchArray(),
        ]);

        Log::info('elasticsearch update '.$this->product->id);
    }
}

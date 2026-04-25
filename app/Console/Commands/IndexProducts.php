<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\TypeSenseService;
use Illuminate\Console\Command;
use Laravel\Scout\Jobs\MakeSearchable;

class IndexProducts extends Command
{
    protected $signature = 'products:index {--chunk=500 : Number of products per chunk}';

    protected $description = 'Index all products to TypeSense';

    public function __construct(private TypeSenseService $typeSearchService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting product indexing...');

        $chunk = (int)$this->option('chunk');
        $total = Product::where('is_active', true)->count();

        if ($total === 0) {
            $this->warn('No active products found to index');
            return 0;
        }

        $this->info("Found {$total} products to index");

        Product::where('is_active', true)
            ->chunk($chunk, function ($products) use (&$indexed) {
                $indexed ??= 0;
                dispatch(new MakeSearchable($products));
                $indexed += count($products);
                $this->line("Indexed {$indexed} products...");
            });

        $this->info('✓ Product indexing completed successfully');
        return 0;
    }
}

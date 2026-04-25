<?php

namespace App\Console\Commands;

use App\Models\Characteristic;
use App\Services\OptionSearchService;
use Illuminate\Console\Command;

class IndexOptions extends Command
{
    protected $signature = 'options:index';

    protected $description = 'Index all characteristic options for search';

    public function __construct(private OptionSearchService $optionSearchService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting option indexing...');

        $characteristics = Characteristic::where('is_active', true)->get();

        foreach ($characteristics as $characteristic) {
            $this->optionSearchService->indexCharacteristicOptions($characteristic);
            $this->line("Indexed options for: {$characteristic->title['ua'] ?? $characteristic->title['ru']}");
        }

        $this->info('✓ Option indexing completed successfully');
        return 0;
    }
}

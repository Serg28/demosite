<?php

namespace App\Console\Commands;

use App\Models\CharacteristicOption;
use Illuminate\Console\Command;

class CharacteristicOptionsGenerateNewSlugs extends Command
{
    protected $signature = 'options:generate-new-slugs';
    protected $description = 'Generate unique slugs for options where slug is empty or null';

    public function handle(): void
    {
        try {
            // Массовое обновление записей, у которых slug пустой или null
            $updatedCount = CharacteristicOption::whereNull('slug')
                ->orWhere('slug', '')
                ->update([
                    'slug' => CharacteristicOption::raw('id') // Используем raw SQL для обновления slug значением ID
                ]);

            $this->info("Successfully updated $updatedCount options.");
        } catch (\Exception $e) {
            $this->error('An error occurred while updating options: ' . $e->getMessage());
        }
    }
}
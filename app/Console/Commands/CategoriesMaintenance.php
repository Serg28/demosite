<?php

namespace App\Console\Commands;

use App\Jobs\MaintenanceCategories;
use Exception;
use Illuminate\Console\Command;

/**
 * CategoriesMaintenance class.
 */
class CategoriesMaintenance extends Command
{
    protected $signature = 'categories:maintenance';

    protected $description = 'Perform maintenance tasks on categories';

    /**
     * Handle the maintenance process for categories.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            MaintenanceCategories::dispatch();

            $this->info('Фоновый процесс обслуживания категорий запущен');

            return 0;
        } catch (Exception $e) {
            $this->error('Произошла ошибка при обслуживании категорий: ' . $e->getMessage());
            return 1;
        }
    }
}

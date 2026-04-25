<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReindexUpdatedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:reindex-updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex all products (scout && elasticsearch) with updated=1 and reset updated to 0';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Начало транзакции
        \DB::beginTransaction();

        try {
            // Выборка всех продуктов с updated = 1
            Product::where('updated', 1)->searchable();

            // Массовое обновление поля updated c блокировкой на изменение записи
            $productCount = Product::lockForUpdate()->where('updated', 1)->update(['updated' => 0]);

            //Сброс только кеша, если хотя бы один товар обновился
            if ($productCount > 0) {
                //TODO: сброс кеша только по определенному тегу, напр., catalog
                Artisan::call('cache:clear');
                Artisan::call('cache:rebuild_setting_cache');
                Artisan::call('cache:rebuild_pages_cache');
            }

            // Завершение транзакции
            \DB::commit();
            $this->info("Successfully reindexed and updated {$productCount} products.");
        } catch (\Exception $e) {
            // Откат транзакции в случае ошибки
            \DB::rollBack();
            $this->error('Failed to reindex products: ' . $e->getMessage());
            throw $e;
        }
    }
}

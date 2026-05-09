<?php

namespace App\Console\Commands;

use App\Services\Delivery\CarrierAdapterRegistry;
use Illuminate\Console\Command;

class DeliverySyncCommand extends Command
{
    protected $signature = 'delivery:sync
                            {carrier? : Carrier slug: np, ukrposhta, justin, meest, rozetka}
                            {--all : Sync all registered carriers}
                            {--cities-only : Sync only cities}
                            {--warehouses-only : Sync only warehouses}';

    protected $description = 'Sync delivery warehouses and cities from carrier APIs';

    public function handle(CarrierAdapterRegistry $registry): int
    {
        $carrier = $this->argument('carrier');
        $all     = $this->option('all');

        if (! $carrier && ! $all) {
            $this->error('Вкажіть кар\'єр або використайте --all');
            $this->line('Доступні: ' . implode(', ', array_map(fn ($a) => $a->carrier(), $registry->all())));

            return self::FAILURE;
        }

        $adapters = $all ? $registry->all() : [$registry->get($carrier)];

        foreach ($adapters as $adapter) {
            $this->info("Синхронізація: {$adapter->label()} [{$adapter->carrier()}]");

            if (! $this->option('warehouses-only')) {
                $this->line('  ↳ міста...');
                $adapter->syncCities(fn (int $done, int $total) => $this->output->write("\r  ↳ міста: {$done}/{$total}"));
                $this->line('');
            }

            if (! $this->option('cities-only')) {
                $this->line('  ↳ відділення...');
                $adapter->syncWarehouses(fn (int $done, int $total) => $this->output->write("\r  ↳ відділення: {$done}/{$total}"));
                $this->line('');
            }

            $this->info("  ✓ {$adapter->label()} синхронізовано");
        }

        return self::SUCCESS;
    }
}

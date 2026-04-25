<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CharacteristicInitToProduct extends Command
{
    protected $signature = 'characteristic:product';

    protected $description = 'characteristic init to product';

    public function handle(): void
    {
        Log::info('start characteristic generate');

        foreach (Product::cursor() as $product) {
            if (! $product->characteristics->count()) {
                foreach ($product->category->characteristics as $characteristic) {
                    ProductCharacteristicOption::create([
                        'product_id' => $product->id,
                        'characteristic_id' => $characteristic->id,
                        'characteristic_option_id' => $characteristic->options()->orderByRaw('RAND()')->first()->id,
                    ]);
                }
            }
        }

        $this->info('\nDone!');
    }
}

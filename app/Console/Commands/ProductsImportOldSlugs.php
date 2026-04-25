<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductsImportOldSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-old-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update slug in products from old table DB';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $externalProducts = DB::table("products")
                ->join("tmp_product_slugs", function ($join) {
                    $join->on("products.id_1c", "=", "tmp_product_slugs.id_1c");
                    //->where(function ($query) {
                    //    $query->whereNull('products.slug')->orWhere('products.slug', '');
                    //});
                })
                ->select([
                    //"categories.id_1c as id_1c_cat",
                    "tmp_product_slugs.id_1c as id_1c_tmp",
                    //"categories.slug as slug_cat",
                    "tmp_product_slugs.slug as slug_tmp"
                ])
                ->get();

            // Update each category in the local DB
            foreach ($externalProducts as $externalProduct) {
                $product = Product::where('id_1c', $externalProduct->id_1c_tmp)->first();
                if ($product) {
                    $product->slug = $externalProduct->slug_tmp;
                    $product->save();
                }
                /*
                 DB::table("categories")
                    ->where("id_1c", $externalCategory->id_1c_tmp)
                    ->update([
                        "slug" => $externalCategory->slug_tmp
                    ]);
                 */

            }
            $this->info('Products slug updated successfully!');

            return 0;

        } catch (\Exception $e) {
            // Output the exception message
            $this->error('An error occurred: ' . $e->getMessage());

            return 1;
        }
    }
}
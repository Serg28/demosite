<?php

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=ProductSlugSeeder
     *
     * @return void
     */
    public function run()
    {
        $products = Product::get();

        foreach ($products as $product) {
            $slug = Str::slug(strip_tags($product->title));

            Product::whereSlug($slug)->exists() ?
                Product::where('id', $product->id)->update(['slug' => $slug.time()]) :
                Product::where('id', $product->id)->update(['slug' => $slug]);
        }
    }
}

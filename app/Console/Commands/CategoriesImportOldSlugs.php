<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CategoriesImportOldSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:import-old-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update slug in categories from old table DB';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $externalCategories = DB::table("categories")
                ->join("tmp_category_slug", function ($join) {
                    $join->on("categories.id_1c", "=", "tmp_category_slug.id_1c")
                        ->where('categories.id', '!=', 1);
                })
                ->select([
                    //"categories.id_1c as id_1c_cat",
                    "tmp_category_slug.id_1c as id_1c_tmp",
                    //"categories.slug as slug_cat",
                    "tmp_category_slug.slug as slug_tmp"
                ])
                ->get();

            // Update each category in the local DB
            foreach ($externalCategories as $externalCategory) {
                DB::table("categories")
                    ->where("id_1c", $externalCategory->id_1c_tmp)
                    ->update([
                        "slug" => $externalCategory->slug_tmp
                    ]);
            }

            $this->info('Categories slug updated successfully!');

            return 0;

        } catch (\Exception $e) {
            // Output the exception message
            $this->error('An error occurred: ' . $e->getMessage());

            return 1;
        }
    }
}
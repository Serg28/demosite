<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class DeleteDataCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=DeleteDataCitiesSeeder
     *
     * @return void
     */
    public function run()
    {
        City::query()->delete();
    }
}

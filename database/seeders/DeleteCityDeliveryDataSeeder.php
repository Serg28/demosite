<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeleteCityDeliveryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=DeleteCityDeliveryDataSeeder
     *
     * @return void
     */
    public function run()
    {
        DB::table('city_delivery')->delete();
    }
}

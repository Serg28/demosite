<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class MeestDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MeestDeliverySeeder
     *
     * @return void
     */
    public function run()
    {
        Delivery::create([
            'title' => '{"en": "Meest", "ru": "Meest", "ua": "Meest"}',
            'price' => '0',
            'is_active' => '1',
            'priority' => '4',
            'type' => 'meest',
            'is_show_for_all_cities' => '0',
            'description' => '{"ua":"за тарифами Meest ","en":"for the tariffs of Meest","ru":"за тарифами Meest"}',
            'description_ru' => '',
            'description_en' => '',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class UkrPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UkrPostSeeder
     *
     * @return void
     */
    public function run()
    {
        Delivery::create([
            'title' => '{"en": "Ukrposhta", "ru": "Укрпочта", "ua": "Укрпочта"}',
            'price' => '0',
            'is_active' => '1',
            'priority' => '5',
            'type' => 'ukrposhta',
            'is_show_for_all_cities' => '0',
            'description' => '{"ua":"","en":"","ru":""}',
            'description_ru' => '',
            'description_en' => '',
        ]);
    }
}

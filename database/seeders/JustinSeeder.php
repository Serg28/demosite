<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class JustinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=JustinSeeder
     *
     * @return void
     */
    public function run()
    {
        Delivery::create([
            'title' => '{"en": "Justin", "ru": "Justin", "ua": "Justin"}',
            'price' => '0',
            'is_active' => '1',
            'priority' => '6',
            'type' => 'justin',
            'is_show_for_all_cities' => '0',
            'description' => '{"ua":"","en":"","ru":""}',
            'description_ru' => '',
            'description_en' => '',
        ]);
    }
}

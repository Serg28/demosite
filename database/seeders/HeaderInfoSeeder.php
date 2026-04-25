<?php

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class HeaderInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=HeaderInfoSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'type' => 0,
            'title' => 'Email в шапке',
            'slug' => 'email-v-header',
            'value' => 'test@i.ua',
            'group_type' => 'general',
        ]);

        Setting::create([
            'type' => 0,
            'title' => 'Телефон в шапке',
            'slug' => 'phones-v-header',
            'value' => '093-434-22-22,093-434-22-22,093-434-22-22',
            'group_type' => 'general',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CurrencySeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 'text_with_languages',
            'title' => 'Валюта',
            'slug' => 'currency',
            'value' => '',
            'group' => 'currency',
            'value_languages' => '{"en": "uah", "ru": "грн", "ua": "грн"}',
            'file' => '',
            'check' => '0',
            'textarea_with_languages' => '{"en": "", "ru": "", "ua": ""}',
            'froala_with_languages' => '{"en": "", "ru": "", "ua": ""}',
        ]);
    }
}

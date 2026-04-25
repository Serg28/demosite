<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class PageCodeSeeder extends Seeder
{
    private $valueLanguages = '{"en": "", "ru": "", "ua": ""}';

    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=PageCodeSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 'text',
                'title' => 'Код head',
                'slug' => 'head',
                'value' => '',
                'group' => 'code',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'text',
                'title' => 'Код body',
                'slug' => 'body',
                'value' => '',
                'group' => 'code',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class LogoForLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 'file',
                'title' => 'Лого в письме',
                'slug' => 'logo_for_letter',
                'value' => '{"en": "", "ru": "", "ua": ""}',
                'group' => 'general',
                'value_languages' => '{"en": "", "ru": "", "ua": ""}',
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => '{"en": "", "ru": "", "ua": ""}',
                'froala_with_languages' => '{"en": "", "ru": "", "ua": ""}',
            ],
        ]);
    }
}

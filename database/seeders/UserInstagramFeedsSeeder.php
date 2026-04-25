<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class UserInstagramFeedsSeeder extends Seeder
{
    private $valueLanguages = '{"en": "", "ru": "", "ua": ""}';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 'text',
            'title' => 'Имя user для instagram',
            'slug' => 'user_instagram_feeds',
            'value' => 'mercurio',
            'group' => 'general',
            'value_languages' => $this->valueLanguages,
            'file' => '',
            'check' => '0',
            'textarea_with_languages' => $this->valueLanguages,
            'froala_with_languages' => $this->valueLanguages,
        ]);
    }
}

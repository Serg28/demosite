<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class CountInstagramFeedsSeeder extends Seeder
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
            'title' => 'Количество фидов instagram на странице',
            'slug' => 'count_instagram_feeds',
            'value' => 9,
            'group' => 'general',
            'value_languages' => $this->valueLanguages,
            'file' => '',
            'check' => '0',
            'textarea_with_languages' => $this->valueLanguages,
            'froala_with_languages' => $this->valueLanguages,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class TurbosmsSeeder extends Seeder
{
    private $valueLanguages = '{"en": "", "ru": "", "ua": ""}';

    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=TurbosmsSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 'text',
                'title' => 'Ключ апи турбо смс',
                'slug' => 'api_key_turbo_sms',
                'value' => '',
                'group' => 'order',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'checkbox',
                'title' => 'Sms сообщения турбо смс',
                'slug' => 'sms_turbo_sms',
                'value' => '',
                'group' => 'order',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'checkbox',
                'title' => 'Viber сообщения турбо смс',
                'slug' => 'viber_turbo_sms',
                'value' => '',
                'group' => 'order',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
        ]);
    }
}

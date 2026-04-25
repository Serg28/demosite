<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class BitrixSeeder extends Seeder
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
            [
                'type' => 'text',
                'title' => 'Битрикс вебхук',
                'slug' => 'bitrix-vebhuk',
                'value' => 'https://mercuriocms.bitrix24.ua/rest/3/1wizul44k4e7y51m/',
                'group' => 'order',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'checkbox',
                'title' => 'Отправка в Битрикс',
                'slug' => 'otpravka-v-bitrix',
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

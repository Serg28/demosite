<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class SettingInternetMarketing extends Seeder
{
    private $valueLanguages = '{"en": "", "ru": "", "ua": ""}';

    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=SettingInternetMarketing
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 'checkbox',
                'title' => 'Выкл/вкл Universal Google Analytics',
                'slug' => 'checkbox_google_analytics',
                'value' => '',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'text',
                'title' => 'Код для Universal Google Analytics',
                'slug' => 'code_google_analytics',
                'value' => 'UA-1234567-1',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'checkbox',
                'title' => 'Выкл/вкл Universal Google Analytics 4',
                'slug' => 'checkbox_google_analytics_four',
                'value' => '',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'text',
                'title' => 'Код для Universal Google Analytics 4',
                'slug' => 'code_google_analytics_four',
                'value' => 'G-JP5X8QYY18',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'checkbox',
                'title' => 'Выкл/вкл динамического ремаркетинга Google',
                'slug' => 'checkbox_dynamic_remarketing_google',
                'value' => '',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'text',
                'title' => 'Код для динамического ремаркетинга Google',
                'slug' => 'code_dynamic_remarketing_google',
                'value' => 'AW-943094715',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'checkbox',
                'title' => 'Выкл/вкл Facebook Pixel',
                'slug' => 'checkbox_facebook_pixel',
                'value' => '',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'text',
                'title' => 'Код Facebook Pixel',
                'slug' => 'code_facebook_pixel',
                'value' => '1782200348767282',
                'group' => 'internet_marketing',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
        ]);
    }
}

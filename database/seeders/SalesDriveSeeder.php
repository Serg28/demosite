<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class SalesDriveSeeder extends Seeder
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
                'title' => 'Кабинет Sales Drive',
                'slug' => 'cabinet-sales-drive',
                'value' => '',
                'group' => 'integration',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'checkbox',
                'title' => 'Отправка в Sales Drive',
                'slug' => 'otpravka-v-sales-drive',
                'value' => '',
                'group' => 'integration',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'text',
                'title' => 'Формкод Sales Drive',
                'slug' => 'formcode-sales-drive',
                'value' => '',
                'group' => 'integration',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
            [
                'type' => 'text',
                'title' => 'Экспортная линка',
                'slug' => 'export-link-sales-drive',
                'value' => '',
                'group' => 'integration',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
        ]);
    }
}

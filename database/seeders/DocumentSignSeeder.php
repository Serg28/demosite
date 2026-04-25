<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DocumentSignSeeder extends Seeder
{
    private $valueLanguages = '{"en": "", "ru": "", "ua": ""}';

    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=DocumentSignSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 'file',
                'title' => 'Подпись',
                'slug' => 'document_sign',
                'value' => '{"en": null, "ru": null, "ua": ""}',
                'group' => 'general',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ], [
                'type' => 'file',
                'title' => 'Печать',
                'slug' => 'document_print',
                'value' => '{"en": null, "ru": null, "ua": ""}',
                'group' => 'general',
                'value_languages' => $this->valueLanguages,
                'file' => '',
                'check' => '0',
                'textarea_with_languages' => $this->valueLanguages,
                'froala_with_languages' => $this->valueLanguages,
            ],
        ]);
    }
}

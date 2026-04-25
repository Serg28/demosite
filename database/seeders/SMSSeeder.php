<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class SMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=SMSSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 'text_with_languages',
            'title' => "Дякуємо! Ваше замовлення №(вставить номер) отримано. Ми зв'яжемось з вами найближчим часом",
            'slug' => 'dyakuyemo-vashe-zamovlennya-vtavit-nomer-otrimano-mi-zvyazhimos-z-vami-nayblizhchim-chasom',
            'value' => '',
            'group' => 'messages',
            'value_languages' => '{"en": "Thank you! Your order №[number] has been received. We will contact you shortly ", "ru": "Спасибо! Ваш заказ №[number] получено. Мы свяжемся с вами в ближайшее время ", "ua": "Дякуємо! Ваше замовлення №[number] отримано. Ми зв\'яжемось з вами найближчим часом"}',
            'file' => '',
            'check' => '0',
            'textarea_with_languages' => '{"en": "", "ru": "", "ua": ""}',
            'froala_with_languages' => '{"en": "", "ru": "", "ua": ""}',
        ]);
    }
}

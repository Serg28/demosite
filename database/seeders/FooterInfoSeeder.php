<?php

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class FooterInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=FooterInfoSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 0,
                'title' => 'Короткий опис в футере',
                'slug' => 'footer_short_text',
                'value' => 'Короткий опис продукту компанії або звернення уваги на її логотип',
                'group_type' => 'general',
            ], [
                'type' => 0,
                'title' => 'Link to Twitter',
                'slug' => 'link_to_twitter',
                'value' => 'https://twitter.com/',
                'group_type' => 'general',
            ], [
                'type' => 0,
                'title' => 'Link to Google+',
                'slug' => 'link_to_google_plus',
                'value' => 'https://play.google.com/',
                'group_type' => 'general',
            ], [
                'type' => 0,
                'title' => 'Адрес в футере',
                'slug' => 'footer_address',
                'value' => '123 Street, Old Trafford, New South London , UK',
                'group_type' => 'general',
            ], [
                'type' => 0,
                'title' => 'Все права',
                'slug' => 'footer_all_right',
                'value' => 'Всі права захищені Bestwebcreator',
                'group_type' => 'general',
            ],
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class LogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=LogoSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'type' => 4,
                'title' => 'Лого на странице в шапке',
                'slug' => 'page_header_logo',
                'value' => '/shop3/assets/images/logo_dark.png',
                'group_type' => 'general',
            ], [
                'type' => 4,
                'title' => 'Лого на странице в футере',
                'slug' => 'page_footer_logo',
                'value' => '/shop3/assets/images/logo_light.png',
                'group_type' => 'general',
            ], [
                'type' => 4,
                'title' => 'Favicon.ico',
                'slug' => 'favicon_ico',
                'value' => '/shop3/assets/images/favicon.png',
                'group_type' => 'general',
            ],
        ]);
    }
}

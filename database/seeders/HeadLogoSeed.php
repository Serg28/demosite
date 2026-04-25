<?php

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class HeadLogoSeed extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=HeadLogoSeed
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 4,
            'title' => 'Head Logo',
            'slug' => 'head_logo',
            'value' => '/svg/logo.svg',
            'group_type' => 'general',
        ]);
    }
}

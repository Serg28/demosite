<?php

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class TwitterAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=TwitterAccountSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 0,
            'title' => 'Twitter акаунт',
            'slug' => 'twitter_account',
            'value' => '@'.env('APP_NAME'),
            'group_type' => 'general',
        ]);
    }
}

<?php

use App\Models\News;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(News::class, function (Faker $faker) {
    $title = $faker->name;
    return [
        'title' => json_encode([
            'ru' => $title,
            'ua' => $title,
            'en' => $title,
        ]),
        'description' => json_encode([
            'ru' => $faker->paragraph(100),
            'ua' => $faker->paragraph(100),
            'en' => $faker->paragraph(100),
        ]),
        'short_description' => json_encode([
            'ru' => $faker->paragraph(3),
            'ua' => $faker->paragraph(3),
            'en' => $faker->paragraph(3),
        ]),

        'slug' => Str::slug($title),
        //'picture' => $faker->randomElement(['/images/holodilnik-nauchili-raspoznavat-produkty.jpeg', '/images/proverim-ka-chto-vy-tam-svarili.jpeg']),
        'pucture' => $faker->image(),
        'is_active' => 1,
    ];
});
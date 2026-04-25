<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vis\Builder\Setting;

class SettingsMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=SettingsMessagesSeeder
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            'type' => 'text_with_languages',
            'title' => 'Дякуємо за ваше замовлення! Ваше замовлення обробляється і буде виконане протягом 3-6 годин. Після завершення замовлення ви отримаєте підтвердження електронною поштою.',
            'slug' => 'dyakuyemo-za-vashe-zamovlennya-vashe-zamovlennya-obroblyayetsya-i-bude-vikonane-protyagom-3-6-godin-pislya-zavershennya-zamovlennya-vi-otrimayete-pidtverdzhennya-elektronnoyu-poshtoyu-',
            'value' => '',
            'group' => 'messages',
            'value_languages' => '{"en": "Thank you for your order! Your order is processed and will be fulfilled within 3-6 hours. Upon completion of the order, you will receive a confirmation email.", "ru": "Спасибо за ваш заказ! Ваш заказ обрабатывается и будет выполнено в течение 3-6 часов. После завершения заказа вы получите подтверждение по электронной почте.", "ua": "Дякуємо за ваше замовлення! Ваше замовлення обробляється й буде виконане протягом 3-6 годин. Після завершення замовлення ви отримаєте підтвердження електронною поштою."}',
            'file' => '',
            'check' => '0',
            'textarea_with_languages' => '{"en": "", "ru": "", "ua": ""}',
            'froala_with_languages' => '{"en": "", "ru": "", "ua": ""}',
        ]);
    }
}

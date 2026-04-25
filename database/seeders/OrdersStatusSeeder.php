<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrdersStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=OrdersStatusSeeder
     *
     * @return void
     */
    public function run()
    {
        Order::query()->update([
            'status' => 1,
        ]);

        OrderStatus::insert([
            [
                'title' => '{"en": "New", "ru": "Новый", "ua": "Новый"}',
                'priority' => 1,
            ], [
                'title' => '{"en": "Paid", "ru": "Оплачен", "ua": "Оплачен"}',
                'priority' => 2,
            ], [
                'title' => '{"en": "Canceled", "ru": "Отменен", "ua": "Отменен"}',
                'priority' => 3,
            ],
        ]);
    }
}

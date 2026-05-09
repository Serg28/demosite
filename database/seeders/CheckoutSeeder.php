<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\PayMethod;
use Illuminate\Database\Seeder;

class CheckoutSeeder extends Seeder
{
    public function run(): void
    {
        $deliveries = [
            [
                'title'      => ['ua' => 'Нова Пошта (відділення)', 'ru' => 'Новая Почта (отделение)'],
                'slug'       => 'np_branch',
                'price'      => 95.00,
                'free_cost'  => 1500.00,
                'priority'   => 10,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Нова Пошта (поштомат)', 'ru' => 'Новая Почта (почтомат)'],
                'slug'       => 'np_poshtamat',
                'price'      => 75.00,
                'free_cost'  => 1500.00,
                'priority'   => 20,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Укрпошта', 'ru' => 'Укрпочта'],
                'slug'       => 'ukrposhta',
                'price'      => 50.00,
                'free_cost'  => null,
                'priority'   => 30,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Meest (відділення)', 'ru' => 'Meest (отделение)'],
                'slug'       => 'meest',
                'price'      => 0.00,
                'free_cost'  => null,
                'priority'   => 40,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Justin (відділення)', 'ru' => 'Justin (отделение)'],
                'slug'       => 'justin',
                'price'      => 0.00,
                'free_cost'  => null,
                'priority'   => 50,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Rozetka (пункт видачі)', 'ru' => 'Rozetka (пункт выдачи)'],
                'slug'       => 'rozetka',
                'price'      => 0.00,
                'free_cost'  => null,
                'priority'   => 60,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Кур\'єр по місту', 'ru' => 'Курьер по городу'],
                'slug'       => 'courier',
                'price'      => 150.00,
                'free_cost'  => 2000.00,
                'priority'   => 70,
                'is_active'  => true,
            ],
            [
                'title'      => ['ua' => 'Самовивіз', 'ru' => 'Самовывоз'],
                'slug'       => 'pickup',
                'price'      => 0.00,
                'free_cost'  => null,
                'priority'   => 80,
                'is_active'  => true,
            ],
        ];

        foreach ($deliveries as $data) {
            $data['title'] = json_encode($data['title']);
            Delivery::firstOrCreate(['slug' => $data['slug']], $data);
        }

        $payMethods = [
            [
                'title'              => ['ua' => '💳 Онлайн-оплата карткою', 'ru' => '💳 Онлайн-оплата картой'],
                'slug'               => 'liqpay',
                'gateway'            => 'liqpay',
                'commission_percent' => 2.75,
                'priority'           => 10,
                'is_active'          => true,
            ],
            [
                'title'              => ['ua' => '🏦 Частинами Mono Bank', 'ru' => '🏦 Частями Mono Bank'],
                'slug'               => 'monopayparts',
                'gateway'            => 'monopayparts',
                'commission_percent' => 0.00,
                'priority'           => 20,
                'is_active'          => true,
            ],
            [
                'title'              => ['ua' => '🏦 Частинами Приват Банк', 'ru' => '🏦 Частями Приват Банк'],
                'slug'               => 'privatpayparts',
                'gateway'            => 'privatpayparts',
                'commission_percent' => 0.00,
                'priority'           => 30,
                'is_active'          => true,
            ],
            [
                'title'              => ['ua' => '📦 Накладений платіж', 'ru' => '📦 Наложенный платёж'],
                'slug'               => 'liqpay_cod',
                'gateway'            => 'liqpay_cod',
                'commission_percent' => 2.00,
                'priority'           => 40,
                'is_active'          => true,
            ],
            [
                'title'              => ['ua' => '🏢 Безготівковий розрахунок', 'ru' => '🏢 Безналичный расчёт'],
                'slug'               => 'paylink',
                'gateway'            => 'paylink',
                'commission_percent' => 0.00,
                'priority'           => 50,
                'is_active'          => true,
            ],
        ];

        foreach ($payMethods as $data) {
            $data['title'] = json_encode($data['title']);
            PayMethod::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Прив'язуємо методи оплати до доставок (усі до всіх)
        $allDeliveries = Delivery::all();
        $allPayMethods = PayMethod::all();

        foreach ($allDeliveries as $delivery) {
            foreach ($allPayMethods as $payMethod) {
                $delivery->payments()->syncWithoutDetaching([$payMethod->id]);
            }
        }
    }
}

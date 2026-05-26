<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryPickupPoint;
use App\Models\DeliveryWarehouse;
use App\Models\DiscountCard;
use App\Models\GiftCertificate;
use App\Models\PayMethod;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckoutDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cities')->truncate();
        DB::table('deliveries')->truncate();
        DB::table('city_delivery')->truncate();
        DB::table('delivery_warehouses')->truncate();
        DB::table('delivery_pickup_points')->truncate();
        DB::table('pay_methods')->truncate();
        DB::table('delivery_payment')->truncate();
        DB::table('promo_codes')->truncate();
        DB::table('discount_cards')->truncate();
        DB::table('gift_certificates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->seedDeliveries();
        $this->seedCities();
        $this->seedPickupPoints();
        $this->seedPayMethods();
        $this->seedPromoCodes();
        $this->seedDiscountCards();
        $this->seedGiftCertificates();
    }

    private function seedDeliveries(): void
    {
        $deliveries = [
            [
                'title'             => json_encode(['ua' => 'Нова Пошта (відділення)', 'ru' => 'Новая Почта (отделение)', 'en' => 'Nova Post (branch)']),
                'slug'              => 'np_branch',
                'price'             => 95.00,
                'free_cost'         => 2000.00,
                'is_for_all_cities' => true,
                'priority'          => 10,
                'is_active'         => true,
            ],
            [
                'title'             => json_encode(['ua' => 'Нова Пошта (поштомат)', 'ru' => 'Новая Почта (постомат)', 'en' => 'Nova Post (parcel locker)']),
                'slug'              => 'np_poshtamat',
                'price'             => 75.00,
                'free_cost'         => 2000.00,
                'is_for_all_cities' => true,
                'priority'          => 20,
                'is_active'         => true,
            ],
            [
                'title'             => json_encode(['ua' => 'Нова Пошта (адресна)', 'ru' => 'Новая Почта (адресная)', 'en' => 'Nova Post (address)']),
                'slug'              => 'np_address',
                'price'             => 120.00,
                'free_cost'         => 3000.00,
                'is_for_all_cities' => true,
                'priority'          => 25,
                'is_active'         => true,
            ],
            [
                'title'             => json_encode(['ua' => 'Meest', 'ru' => 'Meest', 'en' => 'Meest']),
                'slug'              => 'meest',
                'price'             => 0.00,
                'free_cost'         => null,
                'is_for_all_cities' => true,
                'priority'          => 30,
                'is_active'         => true,
            ],
            [
                'title'             => json_encode(['ua' => 'Самовивіз', 'ru' => 'Самовывоз', 'en' => 'Pickup']),
                'slug'              => 'pickup',
                'price'             => 0.00,
                'free_cost'         => null,
                'is_for_all_cities' => true,
                'priority'          => 40,
                'is_active'         => true,
            ],
        ];

        foreach ($deliveries as $data) {
            Delivery::create($data);
        }
    }

    private function seedCities(): void
    {
        $cities = [
            ['ua' => 'Київ',   'ru' => 'Киев',   'en' => 'Kyiv'],
            ['ua' => 'Харків', 'ru' => 'Харьков', 'en' => 'Kharkiv'],
            ['ua' => 'Одеса',  'ru' => 'Одесса',  'en' => 'Odesa'],
        ];

        foreach ($cities as $title) {
            $city = City::create([
                'title'     => json_encode($title),
                'is_active' => true,
            ]);

            // 3 NP branch warehouses per city
            for ($i = 1; $i <= 3; $i++) {
                DeliveryWarehouse::create([
                    'carrier'     => 'np',
                    'type'        => 'Branch',
                    'city_id'     => $city->id,
                    'carrier_ref' => 'demo-np-' . $city->id . '-' . $i,
                    'title'       => json_encode(['ua' => "Відділення №{$i}", 'ru' => "Отделение №{$i}"]),
                    'address'     => "вул. Демонстраційна, {$i}",
                    'is_active'   => true,
                ]);
            }

            // 2 NP poshtamat per city
            for ($i = 1; $i <= 2; $i++) {
                DeliveryWarehouse::create([
                    'carrier'     => 'np',
                    'type'        => 'PostomatV3',
                    'city_id'     => $city->id,
                    'carrier_ref' => 'demo-npm-' . $city->id . '-' . $i,
                    'title'       => json_encode(['ua' => "Поштомат №{$i}", 'ru' => "Постомат №{$i}"]),
                    'address'     => "ТЦ Демо, поверх {$i}",
                    'is_active'   => true,
                ]);
            }
        }
    }

    private function seedPickupPoints(): void
    {
        $pickup = Delivery::where('slug', 'pickup')->first();

        if (! $pickup) {
            return;
        }

        $cities = City::all();

        foreach ($cities as $city) {
            $cityTitle = $city->t('title');
            DeliveryPickupPoint::create([
                'delivery_id' => $pickup->id,
                'city_id'     => $city->id,
                'title'       => json_encode(['ua' => 'Склад ' . $cityTitle, 'ru' => 'Склад ' . $cityTitle]),
                'address'     => 'вул. Складська, 1',
                'priority'    => 10,
                'is_active'   => true,
            ]);
        }
    }

    private function seedPayMethods(): void
    {
        $allDeliveryIds = Delivery::pluck('id')->toArray();
        $npIds = Delivery::whereIn('slug', ['np_branch', 'np_poshtamat'])->pluck('id')->toArray();

        $methods = [
            [
                'title'              => json_encode(['ua' => '💳 Оплата карткою онлайн', 'ru' => '💳 Оплата картой онлайн', 'en' => 'Card payment online']),
                'description'        => json_encode(['ua' => 'Visa, Mastercard, LiqPay', 'ru' => 'Visa, Mastercard, LiqPay', 'en' => 'Visa, Mastercard, LiqPay']),
                'slug'               => 'liqpay',
                'gateway'            => 'liqpay',
                'commission_percent' => 0,
                'commission_rates'   => null,
                'priority'           => 10,
                'is_active'          => true,
                'use_hold'           => false,
                'deliveries'         => $allDeliveryIds,
            ],
            [
                'title'              => json_encode(['ua' => '🏦 Частинами Mono', 'ru' => '🏦 Частями Mono', 'en' => 'Mono installments']),
                'description'        => json_encode(['ua' => 'До 12 міс, 0%', 'ru' => 'До 12 мес, 0%', 'en' => 'Up to 12 months, 0%']),
                'slug'               => 'monopayparts',
                'gateway'            => 'monopayparts',
                'commission_percent' => 0,
                'commission_rates'   => null,
                'priority'           => 20,
                'is_active'          => true,
                'use_hold'           => false,
                'deliveries'         => $npIds,
            ],
            [
                'title'              => json_encode(['ua' => '🏦 Частинами Приват', 'ru' => '🏦 Частями Приват', 'en' => 'Privat installments']),
                'description'        => json_encode(['ua' => 'До 10 міс, 0%', 'ru' => 'До 10 мес, 0%', 'en' => 'Up to 10 months, 0%']),
                'slug'               => 'privatpayparts',
                'gateway'            => 'privatpayparts',
                'commission_percent' => 0,
                'commission_rates'   => null,
                'priority'           => 30,
                'is_active'          => true,
                'use_hold'           => false,
                'deliveries'         => $npIds,
            ],
            [
                'title'              => json_encode(['ua' => '📦 Накладений платіж', 'ru' => '📦 Наложенный платёж', 'en' => 'Cash on delivery']),
                'description'        => json_encode(['ua' => 'Оплата при отриманні', 'ru' => 'Оплата при получении', 'en' => 'Pay on delivery']),
                'slug'               => 'liqpay_cod',
                'gateway'            => 'liqpay_cod',
                'commission_percent' => 2,
                'commission_rates'   => null,
                'priority'           => 40,
                'is_active'          => true,
                'use_hold'           => false,
                'deliveries'         => $npIds,
            ],
        ];

        foreach ($methods as $data) {
            $deliveryIds = $data['deliveries'];
            unset($data['deliveries']);

            $method = PayMethod::create($data);

            foreach ($deliveryIds as $deliveryId) {
                DB::table('delivery_payment')->insert([
                    'delivery_id' => $deliveryId,
                    'payment_id'  => $method->id,
                ]);
            }
        }
    }

    private function seedPromoCodes(): void
    {
        // Багаторазовий відсотковий
        PromoCode::create([
            'code'                   => 'DEMO10',
            'type'                   => 'percent',
            'value'                  => 10,
            'is_active'              => true,
            'usage_type'             => 'reusable',
            'is_used'                => false,
            'used_count'             => 0,
            'use_for_installments'   => true,
            'use_for_promotional'    => true,
            'use_for_discount_cards' => true,
        ]);

        // Фіксована знижка з мінімальною сумою
        PromoCode::create([
            'code'                   => 'SAVE200',
            'type'                   => 'fixed',
            'value'                  => 200,
            'min_order_amount'       => 1000,
            'is_active'              => true,
            'usage_type'             => 'reusable',
            'is_used'                => false,
            'used_count'             => 0,
            'use_for_installments'   => true,
            'use_for_promotional'    => false,
            'use_for_discount_cards' => false,
        ]);

        // Одноразовий
        PromoCode::create([
            'code'                   => 'ONCE15',
            'type'                   => 'percent',
            'value'                  => 15,
            'is_active'              => true,
            'usage_type'             => 'once',
            'is_used'                => false,
            'used_count'             => 0,
            'use_for_installments'   => false,
            'use_for_promotional'    => false,
            'use_for_discount_cards' => false,
        ]);

        // Прострочений (для перевірки помилки)
        PromoCode::create([
            'code'                   => 'EXPIRED',
            'type'                   => 'percent',
            'value'                  => 20,
            'expires_at'             => now()->subDay(),
            'is_active'              => true,
            'usage_type'             => 'reusable',
            'is_used'                => false,
            'used_count'             => 0,
            'use_for_installments'   => true,
            'use_for_promotional'    => true,
            'use_for_discount_cards' => true,
        ]);
    }

    private function seedDiscountCards(): void
    {
        // Знайде за телефоном +380671234567 / 0671234567
        DiscountCard::create([
            'code'      => 'CARD-TEST-001',
            'type'      => 'percent',
            'value'     => 5,
            'phone'     => '+380671234567',
            'name'      => 'Тестовий клієнт',
            'is_active' => true,
            'use_for_installments' => true,
            'use_for_promotional'  => true,
        ]);

        // Знайде за телефоном +380501112233 / 0501112233
        DiscountCard::create([
            'code'      => 'CARD-TEST-002',
            'type'      => 'fixed',
            'value'     => 100,
            'phone'     => '+380501112233',
            'name'      => 'Другий клієнт',
            'is_active' => true,
            'use_for_installments' => false,
            'use_for_promotional'  => false,
        ]);
    }

    private function seedGiftCertificates(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFT-DEMO-001',
            'amount'    => 500,
            'is_active' => true,
            'is_used'   => false,
        ]);

        GiftCertificate::create([
            'code'      => 'GIFT-DEMO-002',
            'amount'    => 1000,
            'is_active' => true,
            'is_used'   => false,
        ]);
    }
}

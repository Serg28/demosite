<?php

namespace Database\Seeders;

use App\Models\Checkout;
use App\Models\PayMethod;
use Illuminate\Database\Seeder;

class EasyPaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=EasyPaySeeder
     *
     * @return void
     */
    public function run()
    {
        $checkout = $this->saveCheckout();

        $this->savePayMethod($checkout->id);
    }

    public function saveCheckout()
    {
        $title = $this->jsonTitle('EasyPay', 'EasyPay');

        $data = [
            'title' => $title,
            'slug' => 'easypay',
            'is_active' => 1,
        ];

        return Checkout::create($data);
    }

    public function savePayMethod($id)
    {
        $title = $this->jsonTitle('Онлайн (EasyPay)', 'Online (EasyPay)');

        PayMethod::create([
            'title' => $title,
            'is_active' => 1,
            'checkout_id' => $id,
        ]);
    }

    public function jsonTitle($title, $titleEn)
    {
        return json_encode([
            'en' => $titleEn,
            'ru' => $title,
            'ua' => $title,
        ]);
    }
}

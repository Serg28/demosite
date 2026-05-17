<?php

namespace Tests\Feature\Checkout;

use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryWarehouse;
use App\Models\PayMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    // ─── Cities ─────────────────────────────────────────────────────────────

    public function test_search_cities_returns_matches(): void
    {
        City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);
        City::create(['title' => json_encode(['ua' => 'Харків', 'ru' => 'Харьков']), 'is_active' => true]);

        $response = $this->getJson('/api/v1/checkout/cities?q=Ки');

        $response->assertOk()->assertJsonCount(1)->assertJsonStructure([['id', 'title']]);
    }

    public function test_search_cities_returns_empty_for_short_query(): void
    {
        City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);

        $this->getJson('/api/v1/checkout/cities?q=К')->assertOk()->assertJsonCount(0);
    }

    public function test_search_cities_returns_empty_for_inactive_city(): void
    {
        City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => false]);

        $this->getJson('/api/v1/checkout/cities?q=Ки')->assertOk()->assertJsonCount(0);
    }

    // ─── Deliveries ──────────────────────────────────────────────────────────

    public function test_deliveries_for_city_returns_active_deliveries(): void
    {
        $city = City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);
        Delivery::create([
            'title'             => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'              => 'np_branch',
            'price'             => 0,
            'free_cost'         => 1000,
            'is_for_all_cities' => true,
            'is_active'         => true,
            'priority'          => 10,
        ]);

        $response = $this->getJson("/api/v1/checkout/deliveries?city_id={$city->id}");

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['slug' => 'np_branch', 'free_from' => 1000.0]);
    }

    public function test_deliveries_returns_empty_without_city_id(): void
    {
        $this->getJson('/api/v1/checkout/deliveries')->assertOk()->assertJsonCount(0);
    }

    public function test_deliveries_skips_inactive(): void
    {
        $city = City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);
        Delivery::create([
            'title'             => json_encode(['ua' => 'Неактивна', 'ru' => 'Неактивная']),
            'slug'              => 'inactive',
            'price'             => 0,
            'free_cost'         => 0,
            'is_for_all_cities' => true,
            'is_active'         => false,
            'priority'          => 10,
        ]);

        $this->getJson("/api/v1/checkout/deliveries?city_id={$city->id}")->assertOk()->assertJsonCount(0);
    }

    public function test_deliveries_response_structure(): void
    {
        $city = City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);
        Delivery::create([
            'title'             => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'              => 'np_branch',
            'price'             => 95.0,
            'free_cost'         => 1500.0,
            'is_for_all_cities' => true,
            'is_active'         => true,
            'priority'          => 10,
        ]);

        $this->getJson("/api/v1/checkout/deliveries?city_id={$city->id}")
            ->assertOk()
            ->assertJsonStructure([['id', 'title', 'slug', 'price', 'free_from', 'description']]);
    }

    // ─── Payments ────────────────────────────────────────────────────────────

    public function test_payments_for_delivery_returns_active_methods(): void
    {
        $delivery = Delivery::create([
            'title'             => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'              => 'np_branch',
            'price'             => 0,
            'free_cost'         => 0,
            'is_for_all_cities' => true,
            'is_active'         => true,
            'priority'          => 10,
        ]);

        $method = PayMethod::create([
            'title'              => json_encode(['ua' => 'Карткою', 'ru' => 'Картой']),
            'slug'               => 'liqpay',
            'gateway'            => 'liqpay',
            'commission_percent' => 2.5,
            'priority'           => 10,
            'is_active'          => true,
            'use_hold'           => false,
        ]);

        $delivery->payments()->attach($method->id);

        $response = $this->getJson("/api/v1/checkout/payments?delivery_id={$delivery->id}");

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['slug' => 'liqpay', 'gateway' => 'liqpay', 'commission_percent' => 2.5]);
    }

    public function test_payments_returns_empty_without_delivery_id(): void
    {
        $this->getJson('/api/v1/checkout/payments')->assertOk()->assertJsonCount(0);
    }

    public function test_payments_returns_empty_for_unknown_delivery(): void
    {
        $this->getJson('/api/v1/checkout/payments?delivery_id=99999')->assertOk()->assertJsonCount(0);
    }

    public function test_payments_skips_inactive_methods(): void
    {
        $delivery = Delivery::create([
            'title'             => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'              => 'np_branch',
            'price'             => 0,
            'free_cost'         => 0,
            'is_for_all_cities' => true,
            'is_active'         => true,
            'priority'          => 10,
        ]);

        $method = PayMethod::create([
            'title'              => json_encode(['ua' => 'Готівка', 'ru' => 'Наличные']),
            'slug'               => 'cash',
            'gateway'            => '',
            'commission_percent' => 0,
            'priority'           => 10,
            'is_active'          => false,
            'use_hold'           => false,
        ]);

        $delivery->payments()->attach($method->id);

        $this->getJson("/api/v1/checkout/payments?delivery_id={$delivery->id}")->assertOk()->assertJsonCount(0);
    }

    public function test_payments_response_structure(): void
    {
        $delivery = Delivery::create([
            'title'             => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'              => 'np_branch',
            'price'             => 0,
            'free_cost'         => 0,
            'is_for_all_cities' => true,
            'is_active'         => true,
            'priority'          => 10,
        ]);

        $method = PayMethod::create([
            'title'              => json_encode(['ua' => 'Карткою', 'ru' => 'Картой']),
            'slug'               => 'liqpay',
            'gateway'            => 'liqpay',
            'commission_percent' => 2.75,
            'priority'           => 10,
            'is_active'          => true,
            'use_hold'           => false,
        ]);

        $delivery->payments()->attach($method->id);

        $this->getJson("/api/v1/checkout/payments?delivery_id={$delivery->id}")
            ->assertOk()
            ->assertJsonStructure([['id', 'title', 'slug', 'gateway', 'commission_percent', 'use_hold']]);
    }

    // ─── Warehouses ──────────────────────────────────────────────────────────

    public function test_search_warehouses_returns_matches(): void
    {
        $city = City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);

        DeliveryWarehouse::create([
            'title'       => json_encode(['ua' => 'Відділення №1', 'ru' => 'Отделение №1']),
            'carrier'     => DeliveryWarehouse::CARRIER_NP,
            'type'        => DeliveryWarehouse::TYPE_BRANCH,
            'city_id'     => $city->id,
            'carrier_ref' => '',
            'is_active'   => true,
        ]);

        $response = $this->getJson("/api/v1/checkout/warehouses?delivery_slug=np_branch&city_id={$city->id}&q=Відділення");

        $response->assertOk()->assertJsonCount(1)->assertJsonStructure([['id', 'title']]);
    }

    public function test_search_warehouses_returns_empty_for_missing_params(): void
    {
        $this->getJson('/api/v1/checkout/warehouses?q=тест')->assertOk()->assertJsonCount(0);
    }

    public function test_search_warehouses_returns_empty_for_invalid_slug(): void
    {
        $city = City::create(['title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']), 'is_active' => true]);

        $this->getJson("/api/v1/checkout/warehouses?delivery_slug=unknown&city_id={$city->id}&q=тест")
            ->assertOk()
            ->assertJsonCount(0);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\TestCase;

class AdminRoutersTest extends TestCase
{
    public function testSetManager(): void
    {
        $user = $this->authUserAdmin();
        $order = Order::latest()->first();

        $response = $this->get(route('set_manager', $order));

        $order = Order::latest()->first();

        $response->assertStatus(302);
        $this->assertEquals($order->manager_id, $user->id);
    }
}

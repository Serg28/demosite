<?php

namespace Tests\Feature\Auth;

use App\Contracts\SmsProvider;
use App\Services\Sms\Drivers\LogDriver;
use App\Services\Sms\SmsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SmsManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sms_manager_is_bound_in_container(): void
    {
        $this->assertInstanceOf(SmsManager::class, app('sms'));
    }

    public function test_sms_provider_contract_resolves_to_log_driver_by_default(): void
    {
        $provider = app(SmsProvider::class);
        $this->assertInstanceOf(LogDriver::class, $provider);
    }

    public function test_log_driver_writes_to_log(): void
    {
        Log::shouldReceive('channel')->once()->with('sms')->andReturnSelf();
        Log::shouldReceive('info')->once()->with('SMS', ['phone' => '+380991234567', 'message' => 'Test']);

        $driver = new LogDriver();
        $driver->send('+380991234567', 'Test');
    }

    public function test_sms_manager_driver_switching(): void
    {
        $manager = app(SmsManager::class);
        $this->assertEquals('log', $manager->getDefaultDriver());
    }

    public function test_sms_manager_sends_via_default_driver(): void
    {
        Log::shouldReceive('channel')->once()->with('sms')->andReturnSelf();
        Log::shouldReceive('info')->once();

        app(SmsProvider::class)->send('+380991234567', 'Hello');
    }
}

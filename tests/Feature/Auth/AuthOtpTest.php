<?php

namespace Tests\Feature\Auth;

use App\Actions\Auth\SendOtpCode;
use App\Actions\Auth\VerifyOtpCode;
use App\Livewire\Auth\OtpLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class AuthOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_page_loads(): void
    {
        $this->get(route('auth.otp'))
            ->assertOk()
            ->assertSeeLivewire(OtpLogin::class);
    }

    public function test_send_otp_code_stores_in_cache(): void
    {
        $action = app(SendOtpCode::class);
        $action->handle('test@example.com', 'email');

        $this->assertNotNull(Cache::get('otp:test@example.com'));
    }

    public function test_verify_otp_code_returns_true_for_valid_code(): void
    {
        Cache::put('otp:test@example.com', '123456', now()->addMinutes(10));

        $action = app(VerifyOtpCode::class);
        $result = $action->handle('test@example.com', '123456');

        $this->assertTrue($result);
        $this->assertNull(Cache::get('otp:test@example.com'));
    }

    public function test_verify_otp_code_returns_false_for_invalid_code(): void
    {
        Cache::put('otp:test@example.com', '123456', now()->addMinutes(10));

        $action = app(VerifyOtpCode::class);
        $result = $action->handle('test@example.com', '999999');

        $this->assertFalse($result);
    }

    public function test_verify_otp_code_returns_false_for_expired_code(): void
    {
        $action = app(VerifyOtpCode::class);
        $result = $action->handle('nonexistent@example.com', '123456');

        $this->assertFalse($result);
    }

    public function test_otp_login_validates_identifier(): void
    {
        Livewire::test(OtpLogin::class)
            ->set('identifier', '')
            ->call('sendCode')
            ->assertHasErrors(['identifier']);
    }

    public function test_otp_login_verify_shows_error_for_invalid_code(): void
    {
        $identifier = 'user@example.com';
        Cache::put("otp:{$identifier}", '654321', now()->addMinutes(10));

        Livewire::test(OtpLogin::class)
            ->set('step', 2)
            ->set('identifier', $identifier)
            ->set('code', '000000')
            ->call('verify')
            ->assertHasErrors(['code']);
    }

    public function test_otp_login_success_creates_user_and_logs_in(): void
    {
        $identifier = 'newuser@example.com';
        Cache::put("otp:{$identifier}", '123456', now()->addMinutes(10));

        Livewire::test(OtpLogin::class)
            ->set('step', 2)
            ->set('identifier', $identifier)
            ->set('code', '123456')
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => $identifier]);
    }
}

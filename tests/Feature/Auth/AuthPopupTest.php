<?php

namespace Tests\Feature\Auth;

use App\Actions\Auth\SendOtpCode;
use App\Actions\Auth\VerifyOtpCode;
use App\Livewire\Auth\Popup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class AuthPopupTest extends TestCase
{
    use RefreshDatabase;

    // ── switchMode ────────────────────────────────────────────────────────────

    public function test_initial_mode_is_login(): void
    {
        Livewire::test(Popup::class)
            ->assertSet('mode', 'login');
    }

    public function test_switch_mode_changes_mode(): void
    {
        Livewire::test(Popup::class)
            ->call('switchMode', 'register')
            ->assertSet('mode', 'register')
            ->call('switchMode', 'forgot')
            ->assertSet('mode', 'forgot')
            ->call('switchMode', 'login')
            ->assertSet('mode', 'login');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Popup::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertDispatched('closeModal')
            ->assertRedirect();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        Livewire::test(Popup::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_login_validates_required_fields(): void
    {
        Livewire::test(Popup::class)
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        Livewire::test(Popup::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function test_register_with_valid_data(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'register')
            ->set('regName', 'Test User')
            ->set('regEmail', 'test@example.com')
            ->set('regPassword', 'password123')
            ->set('regPassword_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertDispatched('closeModal')
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(Popup::class)
            ->set('mode', 'register')
            ->set('regName', 'New User')
            ->set('regEmail', 'existing@example.com')
            ->set('regPassword', 'password123')
            ->set('regPassword_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['regEmail']);
    }

    public function test_register_fails_with_mismatched_passwords(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'register')
            ->set('regName', 'Test User')
            ->set('regEmail', 'test@example.com')
            ->set('regPassword', 'password123')
            ->set('regPassword_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['regPassword']);
    }

    public function test_register_fails_with_short_password(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'register')
            ->set('regName', 'Test User')
            ->set('regEmail', 'test@example.com')
            ->set('regPassword', 'short')
            ->set('regPassword_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['regPassword']);
    }

    public function test_register_validates_required_fields(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'register')
            ->call('register')
            ->assertHasErrors(['regName', 'regEmail', 'regPassword']);
    }

    // ── Forgot password ───────────────────────────────────────────────────────

    public function test_forgot_password_sends_reset_link(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'user@example.com']);

        Livewire::test(Popup::class)
            ->set('mode', 'forgot')
            ->set('forgotEmail', 'user@example.com')
            ->call('forgotPassword')
            ->assertHasNoErrors()
            ->assertSet('forgotSent', true);
    }

    public function test_forgot_password_validates_email(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'forgot')
            ->call('forgotPassword')
            ->assertHasErrors(['forgotEmail']);
    }

    // ── OTP ───────────────────────────────────────────────────────────────────

    public function test_send_otp_advances_to_step_2(): void
    {
        $this->mock(SendOtpCode::class)
            ->shouldReceive('handle')
            ->once();

        Livewire::test(Popup::class)
            ->set('mode', 'otp')
            ->set('identifier', 'user@example.com')
            ->call('sendOtp')
            ->assertHasNoErrors()
            ->assertSet('otpStep', 2);
    }

    public function test_verify_otp_logs_in_user(): void
    {
        $this->mock(VerifyOtpCode::class)
            ->shouldReceive('handle')
            ->once()
            ->andReturn(true);

        Livewire::test(Popup::class)
            ->set('mode', 'otp')
            ->set('otpStep', 2)
            ->set('identifier', 'user@example.com')
            ->set('code', '123456')
            ->call('verifyOtp')
            ->assertHasNoErrors()
            ->assertDispatched('closeModal')
            ->assertRedirect();
    }

    public function test_verify_otp_fails_with_wrong_code(): void
    {
        $this->mock(VerifyOtpCode::class)
            ->shouldReceive('handle')
            ->once()
            ->andReturn(false);

        Livewire::test(Popup::class)
            ->set('mode', 'otp')
            ->set('otpStep', 2)
            ->set('identifier', 'user@example.com')
            ->set('code', '000000')
            ->call('verifyOtp')
            ->assertHasErrors(['code']);
    }

    public function test_change_identifier_resets_to_step_1(): void
    {
        Livewire::test(Popup::class)
            ->set('mode', 'otp')
            ->set('otpStep', 2)
            ->set('code', '123456')
            ->call('changeIdentifier')
            ->assertSet('otpStep', 1)
            ->assertSet('code', '');
    }
}

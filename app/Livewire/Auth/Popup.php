<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\SendOtpCode;
use App\Actions\Auth\VerifyOtpCode;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Component;

/**
 * Auth popup — login / register / forgot / otp у модальному вікні.
 *
 * Відкривається через ModalManager:
 *   <button data-js-modal data-component="auth.popup">Увійти</button>
 *
 * Режими перемикаються без закриття модалки через switchMode().
 */
class Popup extends Component
{
    /** Поточний режим: login | register | forgot | otp */
    public string $mode = 'login';

    // ── Login ─────────────────────────────────────────────────────────────────
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    // ── Register ──────────────────────────────────────────────────────────────
    public string $regName = '';
    public string $regEmail = '';
    public string $regPassword = '';
    public string $regPassword_confirmation = '';

    // ── Forgot password ───────────────────────────────────────────────────────
    public string $forgotEmail = '';
    public bool $forgotSent = false;

    // ── OTP ───────────────────────────────────────────────────────────────────
    public string $identifier = '';
    public string $code = '';
    public int $otpStep = 1;   // 1: ввести identifier, 2: ввести код
    public int $expiresAt = 0;

    public static function modalMaxWidth(): string
    {
        return 'max-w-sm';
    }

    public function switchMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetValidation();
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited();

        $user = $this->resolveCredentials();

        if (
            Features::canManageTwoFactorAuthentication()
            && $user->hasEnabledTwoFactorAuthentication()
        ) {
            Session::put([
                'login.id'       => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->dispatch('closeModal');
            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->dispatch('closeModal');
        $this->redirect(url()->current());
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(): void
    {
        $this->validate([
            'regName'     => ['required', 'string', 'max:255'],
            'regEmail'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'regPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $this->regName,
            'email'    => $this->regEmail,
            'password' => Hash::make($this->regPassword),
        ]);

        event(new Registered($user));
        Auth::login($user);
        Session::regenerate();

        $this->dispatch('closeModal');
        $this->redirect(url()->current());
    }

    // ── Forgot password ───────────────────────────────────────────────────────

    public function forgotPassword(): void
    {
        $this->validate(['forgotEmail' => ['required', 'string', 'email']]);

        Password::sendResetLink(['email' => $this->forgotEmail]);

        $this->forgotSent = true;
    }

    // ── OTP ───────────────────────────────────────────────────────────────────

    public function sendOtp(SendOtpCode $action): void
    {
        $channel = config('auth_features.otp.channel', 'email');
        $rule    = $channel === 'sms'
            ? 'required|string|regex:/^\+?[0-9]{10,15}$/'
            : 'required|email';

        $this->validate(['identifier' => $rule]);

        $action->handle($this->identifier, $channel);

        $this->otpStep   = 2;
        $this->expiresAt = now()->addMinutes(10)->timestamp;
        $this->code      = '';
        $this->resetValidation('code');
    }

    public function resendOtp(SendOtpCode $action): void
    {
        $this->sendOtp($action);
    }

    public function changeIdentifier(): void
    {
        $this->otpStep = 1;
        $this->code    = '';
        $this->resetValidation('code');
    }

    public function verifyOtp(VerifyOtpCode $action): void
    {
        $this->validate(['code' => ['required', 'string', 'size:6']]);

        if (! $action->handle($this->identifier, $this->code)) {
            $this->addError('code', __t('Невірний або прострочений код'));

            return;
        }

        $channel = config('auth_features.otp.channel', 'email');
        $field   = $channel === 'sms' ? 'phone' : 'email';

        $user = User::firstOrCreate(
            [$field => $this->identifier],
            ['name' => $this->identifier, 'password' => null]
        );

        Auth::login($user);
        Session::regenerate();

        $this->dispatch('closeModal');
        $this->redirect(url()->current());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    protected function resolveCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials([
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        return $user;
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.popup');
    }
}

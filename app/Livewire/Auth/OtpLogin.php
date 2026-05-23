<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\SendOtpCode;
use App\Actions\Auth\VerifyOtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OtpLogin extends Component
{
    public string $identifier = '';
    public string $code = '';
    public int $step = 1;
    public int $expiresAt = 0;

    public function sendCode(SendOtpCode $action): void
    {
        $channel = config('auth_features.otp.channel', 'email');
        $rule = $channel === 'sms' ? 'required|string|regex:/^\+?[0-9]{10,15}$/' : 'required|email';

        $this->validate(['identifier' => $rule]);

        $action->handle($this->identifier, $channel);

        $this->step = 2;
        $this->expiresAt = now()->addMinutes(10)->timestamp;
        $this->code = '';
    }

    public function verify(VerifyOtpCode $action): void
    {
        $this->validate(['code' => ['required', 'string', 'size:6']]);

        if (! $action->handle($this->identifier, $this->code)) {
            $this->addError('code', __t('Невірний або прострочений код'));

            return;
        }

        $channel = config('auth_features.otp.channel', 'email');
        $field = $channel === 'sms' ? 'phone' : 'email';

        $user = User::firstOrCreate(
            [$field => $this->identifier],
            ['name' => $this->identifier, 'password' => null]
        );

        Auth::login($user);
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.otp-login');
    }
}

<?php

namespace App\Livewire\Profile\User;

use App\Models\UserProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Security extends Component
{
    public bool $twoFactorEnabled = false;

    #[Locked]
    public bool $showDisableConfirm = false;

    public function mount(): void
    {
        $this->twoFactorEnabled = Auth::user()->hasTwoFactor();
    }

    public function confirmDisable(): void
    {
        $this->showDisableConfirm = true;
    }

    public function cancelDisable(): void
    {
        $this->showDisableConfirm = false;
    }

    public function disconnectProvider(int $providerId): void
    {
        $user = Auth::user();

        if ($user->providers()->count() <= 1 && ! $user->hasPassword()) {
            session()->flash('security_error', 'Неможливо відключити єдиний спосіб входу');

            return;
        }

        UserProvider::query()
            ->where('id', $providerId)
            ->where('user_id', $user->id)
            ->delete();

        session()->flash('security_success', 'Акаунт відключено');
    }

    public function render(): View
    {
        return view('livewire.profile.user.security', [
            'providers' => Auth::user()->providers()->get(),
        ]);
    }
}

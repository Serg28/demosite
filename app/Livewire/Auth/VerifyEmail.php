<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmail extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        session()->flash('status', 'verification-link-sent');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.verify-email');
    }
}

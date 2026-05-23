<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            ['name' => $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getEmail(), 'email_verified_at' => now()]
        );

        UserProvider::updateOrCreate(
            ['provider' => $provider, 'provider_id' => (string) $socialUser->getId()],
            ['user_id' => $user->id, 'avatar' => $socialUser->getAvatar(), 'name' => $socialUser->getName()]
        );

        Auth::login($user);

        return redirect()->intended(route('profile.index'));
    }
}

<?php

namespace App\Actions\Checkout;

use App\Mail\RegisteredFromCheckoutMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class RegisterUser
{
    public function handle(Order $order): void
    {
        if (auth()->check()) {
            $order->update(['user_id' => auth()->id()]);

            return;
        }

        $email = trim($order->email ?? '');

        if (! $email) {
            return;
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $order->update(['user_id' => $existingUser->id]);

            return;
        }

        if (! $order->register_me) {
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'       => trim("{$order->first_name} {$order->last_name}"),
                'first_name' => $order->first_name,
                'last_name'  => $order->last_name,
                'password'   => Str::random(32), // plain → 'hashed' cast on User model applies bcrypt
            ]
        );

        $order->update(['user_id' => $user->id]);

        if ($user->wasRecentlyCreated) {
            $token = Password::createToken($user);
            Mail::to($email)->queue(new RegisteredFromCheckoutMail($user, $token));
        }
    }
}

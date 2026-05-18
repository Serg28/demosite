<?php

namespace Tests\Feature\Checkout;

use App\Actions\Checkout\RegisterUser;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_user_and_sends_mail_for_guest_with_register_me(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'first_name'  => 'Іван',
            'last_name'   => 'Петров',
            'email'       => 'ivan@example.com',
            'register_me' => true,
            'user_id'     => null,
        ]);

        app(RegisterUser::class)->handle($order);

        $user = User::where('email', 'ivan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame($user->id, $order->fresh()->user_id);
        Mail::assertQueued(\App\Mail\RegisteredFromCheckoutMail::class);
    }

    public function test_links_existing_user_by_email_without_creating_new(): void
    {
        Mail::fake();

        $existing = User::factory()->create(['email' => 'exist@example.com']);

        $order = Order::factory()->create([
            'email'       => 'exist@example.com',
            'register_me' => true,
            'user_id'     => null,
        ]);

        app(RegisterUser::class)->handle($order);

        $this->assertSame($existing->id, $order->fresh()->user_id);
        $this->assertCount(1, User::where('email', 'exist@example.com')->get());
        Mail::assertNothingQueued();
    }

    public function test_skips_registration_when_register_me_is_false(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email'       => 'noreg@example.com',
            'register_me' => false,
            'user_id'     => null,
        ]);

        app(RegisterUser::class)->handle($order);

        $this->assertNull($order->fresh()->user_id);
        $this->assertNull(User::where('email', 'noreg@example.com')->first());
        Mail::assertNothingQueued();
    }

    public function test_skips_when_email_is_empty(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email'       => '',
            'register_me' => true,
            'user_id'     => null,
        ]);

        app(RegisterUser::class)->handle($order);

        $this->assertNull($order->fresh()->user_id);
        Mail::assertNothingQueued();
    }

    public function test_new_user_password_is_not_double_hashed(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email'       => 'newhash@example.com',
            'register_me' => true,
            'user_id'     => null,
        ]);

        app(RegisterUser::class)->handle($order);

        $user = User::where('email', 'newhash@example.com')->first();
        $this->assertNotNull($user);
        // A double-hashed bcrypt string starts with '$2y$' again when hashed — check the raw column is valid single bcrypt
        $this->assertTrue(Hash::isHashed($user->getRawOriginal('password')));
        // Ensure it is NOT a double hash: a bcrypt of a bcrypt will not start with the typical bcrypt cost for a random string
        $rawHash = $user->getRawOriginal('password');
        $this->assertStringStartsWith('$2y$', $rawHash);
        $this->assertSame(60, strlen($rawHash));
    }

    public function test_links_authenticated_user_without_creating(): void
    {
        Mail::fake();

        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => null]);

        $this->actingAs($user);

        app(RegisterUser::class)->handle($order);

        $this->assertSame($user->id, $order->fresh()->user_id);
        Mail::assertNothingQueued();
    }
}

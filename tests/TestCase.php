<?php

namespace Tests;

use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();

        $this->withoutExceptionHandling();
    }

    protected function authUser(): User
    {
        $user = User::latest()->first();

        return Sentinel::login($user);
    }

    protected function authUserAdmin(): User
    {
        $user = User::find(1);

        return Sentinel::login($user);
    }
}

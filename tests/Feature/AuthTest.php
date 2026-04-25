<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testLoginFalse()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'arttttt@fail.com',
            'password' => '123123',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'error']);
    }

    public function testLoginSuccess()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'demo@vis-design.com',
            'password' => '123123',
            'g_recaptcha_response' => 'required',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function testForgotPasswordSuccess()
    {
        $response = $this->post(route('auth.forgot_password'), [
            'email' => 'ufa.capital2@gmail.com',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function testForgotPasswordError()
    {
        $response = $this->post(route('auth.forgot_password'), [
            'email' => 'testtest@gmail.com',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'error']);
    }

    public function testRegistrationSuccess()
    {
        $response = $this->post(route('auth.registration'), [
            'email' => $this->faker->email(),
            'password' => '123123',
            're_password' => '123123',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function testRegistrationError()
    {
        $response = $this->post(route('auth.registration'), [
            'email' => $this->faker->email(),
            'password' => '1231231',
            're_password' => '123123',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'error']);

        $response = $this->post(route('auth.registration'), [
            'email' => 'demo@vis-design.com',
            'password' => '1231231',
            're_password' => '1231231',
            'g_recaptcha_response' => 'required|recaptcha',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'error']);
    }

    public function testLoginPageShow()
    {
        $response = $this->get(route('auth.page.login'));

        $response->assertStatus(200);
    }

    public function testRegistrationPageShow()
    {
        $response = $this->get(route('auth.page.registration'));

        $response->assertStatus(200);
    }

    public function testForgotPasswordPageShow()
    {
        $response = $this->get(route('auth.page.forgot-password'));

        $response->assertStatus(200);
    }

    public function testActivatingUser()
    {
        $user = User::latest()->first();

        $response = $this->get(route('auth.activating_user', [$user, 'code text']));

        $response->assertStatus(200);
    }
}

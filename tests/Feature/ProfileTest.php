<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function testProfilePageNoAuth()
    {
        $response = $this->get(route('profile'));

        $response->assertStatus(302);
    }

    public function testProfilePage()
    {
        $this->authUser();

        $response = $this->get(route('profile'));

        $response->assertStatus(200)->assertSee(__t('Персональні дані'));
    }

    public function testLogout()
    {
        $this->authUser();

        $response = $this->get(route('profile.logout'));

        $response->assertStatus(302)->assertRedirect('/');
    }

    public function testProfileSave()
    {
        $this->authUser();

        $response = $this->post(route('profile.save'), [
            'first_name' => 'first_name_test',
            'last_name' => 'last_name_test',
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
        $this->assertEquals(app('user')->first_name, 'first_name_test');
    }

    public function testProfileSavePassword()
    {
        $this->authUser();

        $response = $this->post(route('profile.save.password'), [
            /*'old_password' => 'old_password',
            'new_password' => 'new_password',
            're_password' => 'new_password',*/
            'old_password' => '111111',
            'new_password' => '222222',
            're_password' => '222222',
            'g_recaptcha_response' => 'g_recaptcha_response',
        ]);

        //$response->assertStatus(302);
        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function testProfileFavorites()
    {
        $this->authUser();
        $product = Product::first();

        $this->post(route('like.add', $product));

        $response = $this->get(route('profile.favorites'));

        $response->assertStatus(200)->assertSee($product->t('title'));
    }

    public function testProfileViewedProductsShow()
    {
        $this->authUser();
        $product = Product::first();

        $this->get($product->getUrl());
        $response = $this->get(route('profile.viewed_products'));

        $response->assertStatus(200);
    }

    public function testProfileDiscount()
    {
        $user = $this->authUser();
        $user->update([
            'discount' => 10,
            'discount_cumulative' => 15,
        ]);

        $response = $this->get(route('profile.discount'));
        $response->assertStatus(200)->assertSee($user->discount, 10);
    }
}

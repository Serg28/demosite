<?php

namespace Tests\Feature;

use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function testSendSubscription(): void
    {
        $email = $this->faker->email;

        $data = [
            'email' => $email,
            'g_recaptcha_response' => 'g_recaptcha_response',
        ];

        $response = $this->post(route('subscription'), $data);

        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('subscription',
            [
                'email' => $email,
            ]
        );
    }
}

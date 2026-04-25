<?php

namespace Tests\Feature;

use Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function testSendFeedback(): void
    {
        $data = [
            'name' => 'test',
            'phone' => 'test',
            'g_recaptcha_response' => 'g_recaptcha_response',
        ];

        $response = $this->post(route('callback'), $data);

        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('feedback',
            [
                'name' => 'test',
                'phone' => 'test',
            ]
        );
    }
}

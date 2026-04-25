<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchTest extends TestCase
{
    public function testLiveSearch()
    {
        $data = [
            'q' => 'тест',
        ];

        $response = $this->post(route('search.live'), $data);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function testIndexSearch()
    {
        $response = $this->get(route('search').'?q=Gimborn');

        $response->assertStatus(200);
    }
}

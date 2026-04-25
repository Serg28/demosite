<?php

namespace Tests\Feature;

use App\Models\Feed;
use Tests\TestCase;

class XmlFeedsTest extends TestCase
{
    public function testXml(): void
    {
        $feeds = Feed::active()->get();

        foreach ($feeds as $feed) {
            $response = $this->get('/xml-feed/'.$feed->feed_name.' .xml');
            $response->assertStatus(200);
        }
    }
}

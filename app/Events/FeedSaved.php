<?php

namespace App\Events;

use App\Models\Feed;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Feed $feed;

    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

}

<?php

namespace App\Events;

use App\Models\SeoGroups;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeoGroupsSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SeoGroups $seogroups;

    public function __construct(SeoGroups $seogroups)
    {
        $this->seogroups = $seogroups;
    }

}

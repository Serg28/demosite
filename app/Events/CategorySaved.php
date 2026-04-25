<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategorySaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

}

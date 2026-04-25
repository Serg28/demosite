<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedCategory extends BaseModel
{
    protected $table = 'feed_category';

    public $timestamps = false;

    public function category(): belongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
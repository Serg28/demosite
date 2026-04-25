<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowPrice extends BaseModel
{
    public $timestamps = false;

    protected $table = 'follow_prices';

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

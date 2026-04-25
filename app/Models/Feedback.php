<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends BaseModel
{
    protected $table = 'feedback';

    protected $fillable = [];

    protected $guarded = ['feedback'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

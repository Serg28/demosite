<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class CancelReason extends BaseModel
{
    protected $table = 'cancel_reason';

    protected $fillable = [];

    public $timestamps = false;

    public function scopeFilterNotDefault(Builder $query): Builder
    {
        return $query->whereNotIn('id', [0, 1]);
    }
}

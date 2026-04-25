<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticSearch extends Model
{
    protected $table = 'statistic_search';

    protected $fillable = [];

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUserFullName(): string
    {
        if ($this->user_id) {
            return '<a href="/admin/users?id='.$this->user_id.'">'.$this->user->getFullName().'</a>';
        }

        return '';
    }
}

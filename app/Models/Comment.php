<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Usamamuneerchaudhary\Commentify\Models\Comment as LvComment;

class Comment extends LvComment
{
    protected $fillable = [
        'name',
        'email',
        'user_id',
        'parent_id',
        'body',
        'rating',
        'minus_text',
        'plus_text',
        'commentable_type',
        'commentable_id',
        'created_at',
        'updated_at',

        'is_active'
    ];

    // Метод для получения товара, если комментарий связан с товаром
    //public function product()
    //{
    //    return $this->commentable_type === 'product' ? $this->commentable : null;
    //}

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratingPercent(): float
    {
        return ($this->rating / 5) * 100;
    }

    public function humanDate(string $field = 'created_at'): string
    {
        $date = $this->{$field};

        if (!($date instanceof Carbon)) {
            return $date;
        }

        return $date->isoFormat('D MMMM GGGG');
    }
}

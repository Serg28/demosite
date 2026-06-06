<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Список бажань користувача.
 * Composite PK: user_id + product_id.
 */
class Wishlist extends Model
{
    /** Composite PK: user_id + product_id, без auto-increment id. */
    public $incrementing = false;

    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = ['user_id', 'product_id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

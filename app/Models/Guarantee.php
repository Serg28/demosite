<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantee extends BaseModel
{
    protected $table = 'guarantee';

    protected $fillable = [];

    public $timestamps = true;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}

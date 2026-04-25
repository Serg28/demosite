<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'picture',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

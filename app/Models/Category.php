<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'depth',
        'title',
        'slug',
        'picture',
        'is_active',
    ];

    protected $casts = [
        'title' => 'json',
        'is_active' => 'boolean',
        'depth' => 'integer',
        'lft' => 'integer',
        'rgt' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

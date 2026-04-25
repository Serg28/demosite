<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Characteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'is_range_type',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'title' => 'json',
        'is_range_type' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(CharacteristicOption::class)->orderBy('priority')->orderBy('title');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_characteristic_options',
            'characteristic_id',
            'product_id'
        )->withPivot('characteristic_option_id');
    }
}

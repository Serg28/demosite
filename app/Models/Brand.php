<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'picture',
        'priority',
        'is_active',
        'external_id',
    ];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];
}

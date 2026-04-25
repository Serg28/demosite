<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CharacteristicOption extends Model
{
    use HasFactory;

    protected $table = 'characteristic_options';

    public $timestamps = false;

    protected $fillable = [
        'characteristic_id',
        'title',
        'slug',
        'value',
        'priority',
    ];

    protected $casts = [
        'title' => 'json',
        'priority' => 'integer',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_characteristic_options',
            'characteristic_option_id',
            'product_id'
        );
    }
}

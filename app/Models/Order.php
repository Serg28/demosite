<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'comment',
        'cost',
        'status',
        'delivery',
        'pay_method',
        'external_id',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

}

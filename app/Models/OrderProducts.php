<?php

namespace App\Models;

class OrderProducts extends BaseModel
{
    protected $table = 'order_products';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = $this->convertCommaToDot($value);
    }

    public function convertCommaToDot($value): array|string
    {
        return str_replace(',', '.', $value);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(OrderProductOption::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class WarehouseOrderProduct extends BaseModel
{
    protected $table = 'warehouse_order_product';

    public $timestamps = true;

    protected $fillable = [];

    protected $guarded = [];

    public function warehouse(): HasOne
    {
        return $this->HasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function product(): HasOne
    {
        return $this->HasOne(Product::class, 'id', 'product_id');
    }

    public function order(): HasOne
    {
        return $this->HasOne(Order::class, 'id', 'order_id');
    }
}

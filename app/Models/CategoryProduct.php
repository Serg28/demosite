<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CategoryProduct
 *
 * @property int $id
 * @property int $category_id
 * @property int $product_id
 * @property Category $category
 * @property Product $product
 */
class CategoryProduct extends Model
{
    protected $table = 'category_product';

    public $timestamps = false;

    protected $casts = [
        'category_id' => 'int',
        'product_id' => 'int',
    ];

    protected $fillable = [
        'category_id',
        'product_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

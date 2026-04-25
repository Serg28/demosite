<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricelistTitle extends BaseModel
{
    protected $table = 'block_pricelist_title';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function rubrics(): HasMany
    {
        return $this->hasMany(PricelistRubric::class)->orderPriority();
    }
}

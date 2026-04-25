<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;
use App\Models\Pricelist;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricelistRubric extends BaseModel
{
    protected $table = 'block_pricelist_rubrics';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function prices(): HasMany
    {
        return $this->hasMany(Pricelist::class)->orderPriority();
    }
}

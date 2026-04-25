<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelTab extends BaseModel
{
    protected $table = 'block_model_tabs';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function parameters(): HasMany
    {
        return $this->hasMany(ModelParameter::class)->orderPriority();
    }
}

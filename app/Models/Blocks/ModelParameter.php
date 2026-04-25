<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class ModelParameter extends BaseModel
{
    protected $table = 'block_model_parameters';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

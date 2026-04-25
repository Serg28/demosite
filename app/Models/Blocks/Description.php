<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Description extends BaseModel
{
    protected $table = 'block_descriptions';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

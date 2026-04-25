<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Stage extends BaseModel
{
    protected $table = 'block_stages';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

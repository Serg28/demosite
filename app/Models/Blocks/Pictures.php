<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Pictures extends BaseModel
{
    protected $table = 'block_multi_pictures';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

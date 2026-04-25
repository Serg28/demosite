<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Picture extends BaseModel
{
    protected $table = 'block_pictures';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

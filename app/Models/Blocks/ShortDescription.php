<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class ShortDescription extends BaseModel
{
    protected $table = 'block_short_description';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

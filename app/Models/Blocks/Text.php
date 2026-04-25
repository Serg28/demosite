<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Text extends BaseModel
{
    protected $table = 'block_text';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

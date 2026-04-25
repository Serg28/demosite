<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Buttons extends BaseModel
{
    protected $table = 'block_buttons';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;
}

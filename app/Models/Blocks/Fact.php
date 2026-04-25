<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Fact extends BaseModel
{
    protected $table = 'block_facts';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

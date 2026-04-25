<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class QuickLinks extends BaseModel
{
    protected $table = 'block_quick_links';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

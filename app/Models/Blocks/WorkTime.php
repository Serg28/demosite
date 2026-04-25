<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class WorkTime extends BaseModel
{
    protected $table = 'block_worktime';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

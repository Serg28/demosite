<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;

class Contact extends BaseModel
{
    protected $table = 'block_contacts';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;
}

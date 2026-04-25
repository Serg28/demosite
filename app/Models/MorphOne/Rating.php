<?php

namespace App\Models\MorphOne;

use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Helpers\Traits\Rememberable;

class Rating extends Model
{
    use Rememberable;

    protected $table = 'ratings';

    protected $guarded = [];
}

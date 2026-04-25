<?php

namespace App\Models;

use Vis\Builder\Tree as TreeBuilder;

class MenuAnchor extends TreeBuilder
{
    public $timestamps = false;

    protected $fillable = [];

    protected $guarded = [];

    public function getCacheTags()
    {
        return ['menu_anchor'];
    }
}

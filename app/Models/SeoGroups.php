<?php

namespace App\Models;

use App\Events\SeoGroupsSaved;
use App\Models\MorphOne\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SeoGroups extends BaseModel
{
    protected $table = 'seo_groups';

    public $timestamps = false;

    protected $fillable = [];

    protected $guarded = [];

    protected $dispatchesEvents = [
        'saved' => SeoGroupsSaved::class,
        'deleted' => SeoGroupsSaved::class,
    ];

    public function getCacheTags()
    {
        return ['seogroups'];
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo');
    }
}

<?php

namespace App\Models;

use App\Models\MorphOne\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SeoUrls extends BaseModel
{
    protected $table = 'seo_urls';

    public $timestamps = false;

    protected $fillable = [];

    protected $guarded = [];

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo');
    }
}

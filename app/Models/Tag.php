<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends BaseModel
{
    protected $fillable = [];

    public $timestamps = false;

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class);
    }

    public function getUrl($locale = ''): string
    {
        $newsTemplate = Tree::template('news')->rememberForever()->cacheTags(['tree'])->first();

        return $newsTemplate->getUrl($locale) . '?tag=' . $this->getUrlOrSlug($locale);
    }
}

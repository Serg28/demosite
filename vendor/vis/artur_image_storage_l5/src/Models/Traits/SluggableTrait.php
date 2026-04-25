<?php

namespace Vis\ImageStorage;

use Illuminate\Support\Str;

trait SluggableTrait
{

    protected static function bootSluggableTrait()
    {
        static::saving(function (AbstractImageStorage $item) {
            $item->setSlug();
        });
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug()
    {
        $this->slug = $this->makeUniqueSlug();
    }

    public function makeUniqueSlug()
    {
        $slug = Str::slug($this->title);

        $slugCheck = false;
        while ($slugCheck === false) {
            $slugCheckQuery = $this->where('slug', 'like', $slug)->where("id", "!=", $this->id)->first();
            $slugCheckQuery ? $slug .= "-1" : $slugCheck = true;
        }
        return $slug;
    }
}

<?php

namespace App\Traits;

trait Sluggable
{
    public function getSlug($replacer = "-", $pattern = "a-z0-9"): string
    {
        $arrayTitle = json_decode($this->title, true);
        $title = $arrayTitle[defaultLanguage()] ?? '';

        return \Str::slug(strip_tags($title), $replacer, $pattern);
    }
}

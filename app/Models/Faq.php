<?php

namespace App\Models;

class Faq extends BaseModel
{
    public $timestamps = false;

    public static function search($keyword = null)
    {
        $locale = app()->getLocale();

        return self::when($keyword, fn ($query) =>
            $query->where("title->{$locale}", 'LIKE', '%' . $keyword . '%')
                ->orWhere("description->{$locale}", 'LIKE', '%' . $keyword . '%')
        )->active();
    }
}

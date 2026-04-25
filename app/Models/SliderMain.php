<?php

namespace App\Models;

class SliderMain extends BaseModel
{
    protected $table = 'slider_main';

    protected $fillable = [];

    public function getUrl(): string
    {
        return $this->t('link');
    }
}

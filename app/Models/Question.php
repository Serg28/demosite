<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Question extends BaseModel
{
    public $timestamps = false;

    public function faq(): HasMany
    {
        return $this->hasMany(Faq::class)->orderPriority();
    }

    public function getFaq(): Collection
    {
        return $this->faq()->active()->get();
    }
}

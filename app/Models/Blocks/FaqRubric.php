<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqRubric extends BaseModel
{
    protected $table = 'block_faq_rubrics';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderPriority();
    }
}

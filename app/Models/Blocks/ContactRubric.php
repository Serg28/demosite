<?php

namespace App\Models\Blocks;

use App\Models\BaseModel;
use App\Models\ContactMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactRubric extends BaseModel
{
    protected $table = 'block_contacts_rubrics';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function contact_manies(): HasMany
    {
        return $this->hasMany(ContactMany::class)->orderPriority();
    }
}

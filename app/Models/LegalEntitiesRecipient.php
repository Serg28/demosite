<?php

namespace App\Models;

class LegalEntitiesRecipient extends BaseModel
{
    protected $table = 'legal_entities_recipients';

    protected $fillable = [];

    public $timestamps = false;

    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }
}

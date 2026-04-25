<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class DiscountCard extends BaseModel
{
    protected $table = 'discount_cards';

    protected $fillable = [];

    protected $guarded = [];
    //public $timestamps = false;

    public function getFullName()
    {
        return $this->code.' '.$this->barcode;
    }

    public function getUrlCms(): string
    {
        return '/admin/discount_cards?id='.$this->id;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
        //return $this->hasOne(User::class);
    }
}

<?php

namespace App\Models;

class Subscription extends BaseModel
{
    protected $table = 'subscription';

    protected $fillable = ['email'];
}

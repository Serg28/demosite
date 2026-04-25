<?php

namespace App\Models;

class SellYourCar extends BaseModel
{
    protected $table = 'sellyourcar';

    protected $fillable = ['name','phone','vin','model','regdate','mileage','picture'];

    //protected $guarded = ['sellyourcar'];
}

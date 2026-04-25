<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class JustinWarehouse extends BaseModel
{
    protected $table = 'justin_warehouse';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    //Атрибут ref, содержащий уникальный UUID в справочнике перевозчика.
    public function getRefAttribute(): ?string
    {
        return $this->attributes['uuid'] ?? null;
    }

    //Атрибут checkoutField, содержащий название поля с ИД склада в массиве с данными заказа (в чекауте) и в таблице orders
    //Важно, чтобы эти названия совпадали - проверьте, чтобы таблица orders и массив данных заказа имели такое же поле
    public function getCheckoutFieldAttribute(): string
    {
        return 'justin_warehouse_id';
    }
}

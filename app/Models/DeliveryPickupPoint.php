<?php

namespace App\Models;

class DeliveryPickupPoint extends BaseModel
{
    protected $table = 'delivery_pickup_points';

    protected $fillable = [];

    public $timestamps = false;

    //Атрибут ref, содержащий уникальный UUID в справочнике перевозчика. В данном случае это просто ID
    public function getRefAttribute(): ?string
    {
        return $this->attributes['id'] ?? null;
    }

    //Атрибут checkoutField, содержащий название поля с ИД склада в массиве с данными заказа (в чекауте) и в таблице orders
    //Важно, чтобы эти названия совпадали - проверьте, чтобы таблица orders и массив данных заказа имели такое же поле
    public function getCheckoutFieldAttribute(): string
    {
        return 'delivery_pickup_point_id';
    }
}

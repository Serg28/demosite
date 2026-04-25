<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PayMethod extends BaseModel
{
    protected $table = 'pay_methods';

    protected $fillable = [];

    public $timestamps = false;

    public static array $paypartsMethodsId = [12, 19]; //ID методов Оплаты частями

    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }

    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(
            Delivery::class,
            'delivery_payment',
            'payment_id',
            'delivery_id'
        );
    }

    public function getPaymentType()
    {
        return $this->type ?? 'CASHLESS';
    }

    public function getPaymentLabel(): ?string
    {
        return ($this->getPaymentType() === 'CASHLESS') ? __t('Карточка') : __t('Наличные');
    }

    public function scopeForDelivery($query, $delivery_id = null)
    {
        return $query->when($delivery_id, fn($query) =>
        $query->whereHas('deliveries', fn($subQuery) => $subQuery->where('delivery_id', $delivery_id))
        );
    }

    /**
     * Получить методы оплаты, у которых минимальная сумма заказа для показа меньше или равно передаваемому значению (напр., сумме заказа).
     *
     */
    public function scopeGreaterThanAmount($query, $amount = 0)
    {
        return $query->where('min_order_amount', '<=', $amount);
    }

    //Возвращает признак, что функционал предоплаты для данного метода оплаты активирова
    public function isPrepaymentActive()
    {
        return !empty($this->is_active_prepayment) && !empty($this->checkout->is_active_prepayment);
    }
}

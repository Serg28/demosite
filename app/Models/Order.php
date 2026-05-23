<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'cost'               => 'float',
            'cost_without_sale'  => 'float',
            'price_delivery'     => 'float',
            'sale'               => 'float',
            'sale_promo'         => 'float',
            'sale_discount'      => 'float',
            'payment_commission' => 'float',
            'call_me'            => 'boolean',
            'register_me'        => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function payMethod(): BelongsTo
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function deliveryWarehouse(): BelongsTo
    {
        return $this->belongsTo(DeliveryWarehouse::class);
    }

    public function deliveryPickupPoint(): BelongsTo
    {
        return $this->belongsTo(DeliveryPickupPoint::class);
    }

    public function paymentInvoices(): HasMany
    {
        return $this->hasMany(PaymentInvoice::class);
    }

    public function giftCertificates(): HasMany
    {
        return $this->hasMany(GiftCertificate::class, 'used_in_order_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getTotalCost(): float
    {
        return $this->products->sum(fn ($item) => $item->amount ?: ($item->price * $item->count));
    }
}

<?php

namespace App\Models;

use App;
use App\Services\Checkouts\EasyPay\EasyPay;
use App\Services\Checkouts\LiqPay\LiqPay;
use App\Services\Order as OrderService;
use Bkwld\Cloner\Cloneable;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use NumberToWords\NumberToWords;

class Order_orig extends BaseModel
{
    use Cloneable;

    protected $table = 'orders';

    protected $cloneable_relations = ['products'];

    protected $fillable = [];

    protected $guarded = [];

    public function getUrl(): string
    {
        return asset('/admin/orders?id='.$this->id);
    }

    public function urlPayment(): string
    {
        return route('payment.init', $this);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id');
    }

    public function complectation(): BelongsTo
    {
        return $this->belongsTo(ComplectStatus::class, 'complect_status_id', 'id');
    }

    public function paymentstatus(): BelongsTo
    {
        return $this->belongsTo(PayStatus::class, 'is_online_payed', 'id');
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function payMethod(): BelongsTo
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function deliveryPickupPoint(): BelongsTo
    {
        return $this->belongsTo(DeliveryPickupPoint::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function npWarehouse(): BelongsTo
    {
        return $this->belongsTo(NPWarehouse::class);
    }

    public function ukrposhtaWarehouse(): BelongsTo
    {
        return $this->belongsTo(UkrposhtaWarehouse::class, 'ukrposhta_warehouse_id');
    }

    public function justinWarehouse(): BelongsTo
    {
        return $this->belongsTo(JustinWarehouse::class);
    }

    public function meestWarehouse(): BelongsTo
    {
        return $this->belongsTo(MeestWarehouse::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProducts::class);
    }

    public function cartOrderProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'order_products'
        )->withPivot(['count', 'price']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(LegalEntitiesRecipient::class, 'legal_entities_recipient_id');
    }

    public function orderUtm(): HasOne
    {
        return $this->hasOne(OrderUtm::class);
    }

    public function orderReceipt(): HasOne
    {
        return $this->hasOne(OrderReceipt::class);
    }

    public function scopeFilterQuickOrder(Builder $query): Builder
    {
        return $query->where('is_quick', 1);
    }

    public function scopeFilterNotQuickOrder(Builder $query): Builder
    {
        return $query->where('is_quick', 0);
    }

    public function afterSave()
    {
        return (new OrderService($this))->saveProducts();
    }

    public function pickUpTheGoods(): string
    {
        if ($this->delivery_pickup_point_id) {
            return '('.$this->deliveryPickupPoint->t('address').')';
        }

        if ($this->np_warehouse_id) {
            return '('.$this->npWarehouse->t('title').')';
        }

        if ($this->ukrposhta_warehouse_id) {
            return '('.$this->ukrposhtaWarehouse->t('title').')';
        }

        if ($this->justin_warehouse_id) {
            return '('.$this->justinWarehouse->t('title').')';
        }

        if ($this->meest_warehouse_id) {
            return '('.$this->meestWarehouse->t('title').')';
        }

        if ($this->address) {
            return $this->address;
        }

        return '';
    }

    public function getSignatureAttribute(): string
    {
        return Hash::make($this->id.$this->created_at);
    }

    public function getPriceForDocumentsAttribute()
    {
        if ($this->is_delivery_paid_separately) {
            return $this->getAllCost();
        }

        return $this->getAllCost() + $this->price_delivery;
    }

    public function checkSignature(string $signature): bool
    {
        return Hash::check($this->id.$this->created_at, $signature);
    }

    public function getAllCost(): int
    {
        return $this->products->sum(function ($item) {
            return $item->price * $item->count;
        });
    }

    public function pay()
    {
        try {
            switch ($this->payMethod->checkout->slug) {
                case 'liqpay':
                    return new LiqPay($this);
                case 'easypay':
                    return new EasyPay($this);
                default:
                    throw new InvalidArgumentException('Pay method not found!');
            }
        } catch (ErrorException $e) {
            throw new InvalidArgumentException('Pay method not found!');
        }
    }

    public function formatDate(string $field = 'created_at', string $format = 'd.m.Y'): string
    {
        $date = $this->{$field};

        if (! ($date instanceof Carbon)) {
            return $date;
        }

        return $date->format($format);
    }

    public function getInfoFromDate($param = false, $field = 'created_at')
    {
        $date = $this->{$field};

        if (! $param && ! ($date instanceof Carbon)) {
            return $date;
        }

        return $date->format($param);
    }

    public function numberToWords()
    {
        $numberToWords = new NumberToWords();

        $numberTransformer = $numberToWords->getNumberTransformer(App::getLocale());

        return $numberTransformer->toWords($this->getPriceForDocumentsAttribute());
    }

    public function clearPhone(): string
    {
        return preg_replace('/[^A-Za-z0-9]/', '', $this->phone); // Removes special chars.
    }

    public function payment()
    {
        return new \App\Services\OrderPayment($this);
    }
}

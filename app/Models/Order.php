<?php

namespace App\Models;

use App;
use App\Enums\DeliveryMethodEnum;
use App\Enums\OrderFieldNameEnum;
use App\Enums\OrderStatusEnum;
use App\Services\Checkouts\EasyPay\EasyPay;
use App\Services\Checkouts\LiqPay\LiqPay;
use App\Services\Checkouts\LiqPay\LiqPayCOD;
use App\Services\Checkouts\MonoPay\MonoPay;
use App\Services\Checkouts\MonoPayParts\MonoPayParts;
use App\Services\Checkouts\PrivatPayParts\PrivatPayParts;
use App\Services\Checkouts\WayForPay\WayForPay;
use App\Services\Order as OrderService;
use App\Services\UtmLabel;
use Bkwld\Cloner\Cloneable;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use NumberToWords\NumberToWords;
use Throwable;

class Order extends BaseModel
{
    use Cloneable, BroadcastsEvents;

    protected $table = 'orders';

    protected $cloneable_relations = ['products'];

    protected $fillable = [];
    /*protected $fillable = [
        "phone",
        "first_name",
        "last_name",
        "email",
        "call_me",
        "receiver",
        "receiver_patronymic_name",
        "city_id",
        "delivery_id",
        "np_warehouse_id",
        "pay_method_id",
        "comment",
        "promo_code",
        "price_delivery",
        "user_id",
        "cost",
        "order_status_id",
        "sale",
        "sale_promo",
        "sale_discount",
        "cost_without_sale",
        "legal_entities_recipient_id",
        "prepayment_amount"
    ];*/

    protected $guarded = [];

    protected $casts = [
        'np_info' => 'array',
        'liqpay_info' => 'array',
        'privat_payparts_info' => 'array',
        'mono_payparts_info' => 'array',
        'order_status_id' => 'integer',
        'cancel_reason_id' => 'integer',
        'complect_status_id' => 'integer',
        'delivery_id' => 'integer',
        'pay_method_id' => 'integer',
    ];

    protected $dates = ['reserved_to'];

    // Список полей, для которых нужно при сохранении устанавливать значение в null, если они пусты
    // Сейчас перечислены поля, имеющие внешний ключ и в случае присваивания им пустого значения могут возвращать ошибку
    // Пока только поля с ИД точек выдачи или складов для доставки
    protected array $nullableIfEmpty = [
        'np_warehouse_id',
        'delivery_pickup_point_id',
        'justin_warehouse_id',
        'ukrposhta_warehouse_id',
        'meest_warehouse_id'
    ];

    protected $appends = ['order_number'];

    protected $revisionFormattedFields = [
        '1' => 'string:<strong>%s</strong>',
        'call_me' => 'boolean:Нi|Так',
        'is_delivery_paid_separately' => 'boolean:Нi|Так',
        'is_online_payed' => 'options:0.Несплачений|1.Сплачений|2.Оплата блокована|3.Очікуємо на оплату',
        'complect_status_id' => 'options:0.Без статуса|1.Очікує комплектацію|2.Замовлено у постачальника|3.Комплектується|4.Укомплектоване|5.Спаковане|6.Готовий до видачі',
        'pay_method_id' => 'options:1.Готівкою кур\'єру|2.Картою онлайн (LiqPay, Privat24, Google/Apple Pay)|3.Онлайн (EasyPay)|4.Безготівкова оплата за реквізитами|5.Готівкова / безготівкова в касу|6.Оплата частинами',
        'order_status_id' => 'options:1.Новий|2.Обробляється|3.Готовий до відправки|4.Самовивіз|5.Відправлений|6.Доставлений|7.Виконаний|8.Відмова від отримання|9.Розформований|10.Скасований|11.Повернення|12.Оплачений',
    ];

    public static function boot()
    {
        parent::boot();

        static::updating(function ($order) {
            if (empty($order->num)) {
                $order->num = $order->id;
            }
        });

        static::creating(function ($order) {
            if (empty($order->num)) {
                $order->num = $order->id;
            }
        });

        static::saving(function ($model) {
            foreach ($model->nullableIfEmpty as $fieldName) {
                $value = $model->{$fieldName};

                if (empty($value)) {
                    $model->{$fieldName} = null;
                }
            }
        });
    }

    /**
     * Устанавливает значение атрибута is_online_payed.
     * Если значение $value равно null, атрибут is_online_payed устанавливается в 0.
     *
     * @param mixed $value Новое значение атрибута is_online_payed.
     * @return void
     */
    public function setIsOnlinePayedAttribute($value): void
    {
        if (array_key_exists('is_online_payed', $this->attributes)) {
            $this->attributes['is_online_payed'] = $value ?? 0;
        }
    }

    /**
     * Устанавливает значение атрибута complect_status_id.
     * Если значение $value равно null, атрибут complect_status_id устанавливается в 0.
     *
     * @param mixed $value Новое значение атрибута complect_status_id.
     * @return void
     */
    public function setComplectStatusIdAttribute($value): void
    {
        if (array_key_exists('complect_status_id', $this->attributes)) {
            $this->attributes['complect_status_id'] = $value ?? 0;
        }
    }

    public function broadcastOn(string $event): array
    {
        return [
            new PrivateChannel('orders-channel'),
        ];
    }

    public function toBroadcast(): array
    {
        return [
            'user' => $this->user,
        ];
    }

    public function broadcastQueue()
    {
        return 'broadcasting';
    }

    public function getRevisionFormattedFieldNames(): array
    {
        return OrderFieldNameEnum::toArray();
    }

    public function getUrl(): string
    {
        return asset('/admin/orders?id=' . $this->id);
    }

    public function getSingleUrl(): string
    {
        return asset('/admin/orderedit?o=' . $this->id);
    }

    public function urlPayment(): string
    {
        return route('payment.init', $this);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id');
    }

    public function cancelReason(): BelongsTo
    {
        return $this->belongsTo(CancelReason::class, 'cancel_reason_id', 'id')->orderBy('priority asc');
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

    public function guarantee(): BelongsTo
    {
        return $this->belongsTo(Guarantee::class);
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
        return $this->belongsTo(User::class)->hasOrderAccess();
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(LegalEntitiesRecipient::class, 'legal_entities_recipient_id');
    }

    public function recipient_default(): BelongsTo
    {
        return $this->belongsTo(LegalEntitiesRecipient::class, 'legal_entities_recipient_id')->where('is_default', 1);
    }

    public function orderUtm(): HasOne
    {
        return $this->hasOne(OrderUtm::class);
    }

    public function orderReceipt(): HasMany//HasOne
    {
        //return $this->hasOne(OrderReceipt::class);
        return $this->hasMany(OrderReceipt::class);
    }

    /**
     * Связь "один ко многим" с моделью PaymentInvoice.
     * Получает все инвойсы, связанные с заказом.
     *
     * @return HasMany
     */
    public function paymentInvoices(): HasMany
    {
        return $this->hasMany(PaymentInvoice::class);
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
        (new UtmLabel())->saveUtmLabels($this);
        return (new OrderService($this))->saveProducts();
    }

    public function pickUpTheGoods(): string
    {
        switch ($this->delivery->type ?? '') {
            case 'pickup':
                return $this->delivery_pickup_point_id ? $this->deliveryPickupPoint->t('address') : '';
            case 'np':
            case 'np_pochtomat':
                return $this->np_warehouse_id ? $this->npWarehouse->t('title') : '';
            case 'ukrposhta':
                return $this->ukrposhta_warehouse_id ? $this->ukrposhtaWarehouse->t('title') : '';
            case 'justin':
                return $this->justin_warehouse_id ? $this->justinWarehouse->t('title') : '';
            case 'meest':
                return $this->meest_warehouse_id ? $this->meestWarehouse->t('title') : '';
            default:
                return $this->address ?? '';
        }
    }

    public function getOrderNumberAttribute()
    {
        return $this->num ?: $this->id;
    }

    /*public function paymentName(): string
    {
        return strip_tags($this->payMethod->t('title'));
    }*/

    public function paymentName(): string
    {
        if ($this->payMethod && method_exists($this->payMethod, 't')) {
            return strip_tags($this->payMethod->t('title'));
        }

        return '';
    }

    public function getSignatureAttribute(): string
    {
        return Hash::make($this->id . $this->created_at);
    }

    /*
    public function getDiscountSaleSum(): float|int
    {
        return $this->sale_discount_amount ?? 0;
    }

    public function getPromoSaleSum(): float|int
    {
        return $this->sale_promo_amount ?? 0;
    }
    */

    //Итоговая сумма скидки
    public function getSaleSum(): int
    {
        return $this->getAllCost() - $this->getTotalCost();
    }

    //Для скидки от суммы заказа
    /*public function getPriceForDocumentsAttribute()
    {
        $sale = $this->getSaleSum();

        if ($this->is_delivery_paid_separately) {
            return $this->getAllCost() - $sale;
        }

        $total = ($this->getAllCost() - $sale) + $this->price_delivery;

        return ($total) ? $total : 0;
    }*/

    public function getPriceForDocumentsAttribute(?bool $moneyFormat = false): int|float|string
    {
        $total = $this->is_delivery_paid_separately ? $this->getTotalCost() : ($this->getTotalCost() + $this->price_delivery);
        $tax = $this->tax ?? 0;

        if ($moneyFormat === true) {
            $total = number_format($total, 2, ".", "");
        }

        return $total + $tax;
    }

    public function checkSignature(string $signature): bool
    {
        return Hash::check($this->id . $this->created_at, $signature);
    }

    /**
     * Метод `getAllCost` возвращает общую стоимость всех товаров в корзине БЕЗ СКИДКИ.
     *
     * Этот метод просматривает все товары в корзине и суммирует их базовые суммы (base_price * count)
     * или если она не определена, то произведение цены на количество товара (price * count)
     * и возвращает результат
     *
     * @return int Общая стоимость товаров в корзине БЕЗ СКИДКИ.
     */
    public function getAllCost(): int
    {
        return $this->products->sum(function ($item) {
            return $item->base_amount ?: (($item->base_price ?: $item->price) * $item->count);
        });
    }

    /**
     * Метод `getTotalCost` возвращает общую стоимость всех товаров в корзине СО СКИДКОЙ.
     *
     * Этот метод просматривает все товары в корзине и суммирует их суммы со скидкой
     * или если она на определена, то произведение цены на количество товара
     * и возвращает результат
     *
     * @return int Общая стоимость товаров в корзине СО СКИДКОЙ
     */
    public function getTotalCost(): int
    {
        return $this->products->sum(function ($item) {
            return $item->total_amount ?: $item->price * $item->count;
        });
    }

    //Сумма предоплаты
    public function getPrepaymentAmount()
    {
        try {
            $pay = $this->pay();

            if ($pay && method_exists($pay, 'getPrepaymentSum')) {
                return $pay->getPrepaymentSum();
            }

            return 0;
        } catch (Throwable $e) {
            return 0;
        }
    }

    //Итоговая стоимость заказа минуc сумма предоплаты
    public function getTotalWithoutPrepayment()
    {
        return $this->getPriceForDocumentsAttribute() - $this->getPrepaymentAmount();
    }

    /**
     * Метод обновляет итоговые суммы текущего заказа в полях `cost_without_sale` и `cost`, если они отличаются
     * от результатов методов `getAllCost()` и `getTotalCost()`. Данный метод предоставляет
     * опцию для тихого сохранения изменений в базе данных.
     *
     * @return bool Возвращает true, если поля были изменены и false, если не было изменений.
     */
    public function updateCostFields(): bool
    {
        $currentCostWithoutSale = $this->cost_without_sale;
        $currentTotalCost = $this->cost;
        $currentTax = $this->tax;

        $allCost = $this->getAllCost();
        $totalCost = $this->getTotalCost() + $currentTax;

        if ($currentCostWithoutSale !== $allCost || $currentTotalCost !== $totalCost) {
            $this->cost_without_sale = $allCost;
            $this->cost = $totalCost;
            $this->save();

            return true;
        }

        return false;
    }

    public function pay()
    {
        try {

            $checkout = $this->payMethod;
            if (!$checkout || !$checkout->checkout) {
                $slug = '';
            } else {
                $slug = $this->payMethod->checkout->slug;
            }

            //switch ($this->payMethod->checkout->slug) {
            switch ($slug) {
                case 'liqpay':
                    return new LiqPay($this);
                case 'liqpaycod':
                    return new LiqPayCOD($this);
                case 'easypay':
                    return new EasyPay($this);
                case 'privatpayparts':
                    return new PrivatPayParts($this);
                case 'monopayparts':
                    return new MonoPayParts($this);
                case 'monopay':
                    return new MonoPay($this);
                case 'wayforpay':
                    return new WayForPay($this);
                default:
                    //throw new InvalidArgumentException('Pay method not found!');
                    return null;
            }
        } catch (ErrorException $e) {
            throw new InvalidArgumentException('Pay method not found!'. $e->getMessage());
        }
    }

    public function formatDate(string $field = 'created_at', string $format = 'd.m.Y'): string
    {
        $date = $this->{$field};

        if (!($date instanceof Carbon)) {
            return $date;
        }

        return $date->format($format);
    }

    public function getInfoFromDate($param = false, $field = 'created_at')
    {
        $date = $this->{$field};

        if (!$param && !($date instanceof Carbon)) {
            return $date;
        }

        return $date->format($param);
    }

    public function numberToWords()
    {
        $numberToWords = new NumberToWords();

        return $numberToWords->getNumberTransformer(App::getLocale())->toWords($this->getPriceForDocumentsAttribute());
    }

    public function clearPhone(): string
    {
        return preg_replace('/[^A-Za-z0-9]/', '', $this->phone); // Removes special chars.
    }

    public function payment()
    {
        return new \App\Services\OrderPayment($this);
    }

    public function is_paid()
    {
        if (
            (in_array(
                    $this->pay_methods,
                    [1, 5]
                ) && $this->is_online_payed == 3) || // В кассу или Курьеру + Ожидаем оплату
            (!in_array($this->pay_methods, [1, 5]) && $this->is_online_payed == 1) //Остальные способы оплаты + Оплачено
        ) {
            return 1;
        }

        return 0;
    }

    //Если доставка через службы доставки (НЕ курьер (3) и НЕ самовывоз (1))
    public function is_delivery_service(): bool
    {
        return !in_array($this->delivery_id, [
            $this->delivery->pickup_id,
            $this->delivery->curier_id
        ]);
    }

    //Если доставка - Новая почта на отделение или адресная
    public function isNovaPoshtaDelivery(): bool
    {
        return in_array($this->delivery_id, [
            DeliveryMethodEnum::NovaPoshtaPickup(), // Новою Поштою (у відділення)
            DeliveryMethodEnum::NovaPoshtaAddress() // Новою поштою (адресна доставка)
        ]);
    }

    /**
     * Scope для исключения статусов "Виконаний", "Відмова від отримання", "Розформований", "Скасований", "Повернення".
     *
     */
    public function scopeExcludeCompletedAndCancelled($query)
    {
        $excludedStatuses = [
            OrderStatusEnum::Completed(), //Виконаний
            OrderStatusEnum::RefusedToReceive(), //Відмова від отримання
            OrderStatusEnum::Disbanded(), //Розформований
            OrderStatusEnum::Canceled(), //Скасований
            OrderStatusEnum::Return(), //Повернення
        ];

        return $query->whereNotIn('order_status_id', $excludedStatuses);
    }

    /**
     * Получить стоимость доставки.
     *
     * @return float|string|int Стоимость доставки или строка 'free', если доставка бесплатная, или 0, если доставка недоступна.
     */
    public function getDeliveryPrice(): float|int|string
    {
        $delivery = $this->delivery;

        if ($delivery) {
            return (($this->getTotalCost() >= $delivery->free_cost) && $delivery->free_cost !== null && $delivery->free_cost !== '' && $delivery->free_cost>0) ? 'free' : $this->price_delivery ?? 0;
        }

        return 0;
    }

    /**
     * Получить текстовое описание стоимости доставки.
     *
     * @return string|null Текстовое описание стоимости доставки или null, если доставка недоступна.
     */
    public function getDeliveryDesc(): ?string
    {
        $delivery = $this->delivery;

        return ($this->getDeliveryPrice() === 'free') ? __t('бесплатно') : (($delivery) ? strip_tags($delivery->t('description')) : '');
    }

    public function statusName()
    {
        return $this->status?->t('title') ?? __t('Неизвестный статус');
    }

    public function statusColor()
    {
        return $this->status?->color ?? '';
    }

    public function paymentStatusName()
    {
        return $this->paymentstatus?->t('title') ?? '';
    }
}

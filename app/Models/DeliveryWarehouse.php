<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryWarehouse extends Model
{
    use HasTranslations;

    const CARRIER_NP = 'np';
    const CARRIER_UKRPOSHTA = 'ukrposhta';
    const CARRIER_JUSTIN = 'justin';
    const CARRIER_MEEST = 'meest';
    const CARRIER_ROZETKA = 'rozetka';

    const TYPE_BRANCH = 'branch';
    const TYPE_POSHTAMAT = 'poshtamat';
    const TYPE_PVZ = 'pvz';

    const SLUG_MAP = [
        'np_branch'    => ['carrier' => self::CARRIER_NP, 'type' => self::TYPE_BRANCH],
        'np_poshtamat' => ['carrier' => self::CARRIER_NP, 'type' => self::TYPE_POSHTAMAT],
        'ukrposhta'    => ['carrier' => self::CARRIER_UKRPOSHTA, 'type' => null],
        'justin'       => ['carrier' => self::CARRIER_JUSTIN, 'type' => null],
        'meest'        => ['carrier' => self::CARRIER_MEEST, 'type' => null],
        'rozetka'      => ['carrier' => self::CARRIER_ROZETKA, 'type' => null],
    ];

    protected $table = 'delivery_warehouses';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    public function scopeForDeliverySlug(Builder $query, string $slug): Builder
    {
        $map = self::SLUG_MAP[$slug] ?? null;

        if ($map === null) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('carrier', $map['carrier']);

        if ($map['type'] !== null) {
            $query->where('type', $map['type']);
        }

        return $query;
    }

    public function scopeForCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    public static function carrierForSlug(string $slug): ?string
    {
        return self::SLUG_MAP[$slug]['carrier'] ?? null;
    }

    public static function hasWarehouseSearch(string $slug): bool
    {
        return isset(self::SLUG_MAP[$slug]);
    }
}

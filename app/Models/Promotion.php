<?php

namespace App\Models;

use App\Services\Filters\FilterUniversal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Promotion extends BaseModel
{
    protected $table = 'promotions';

    protected $fillable = [];

    protected $guarded = [];

    private array $characteristics = [
        15010 //Brand
    ];

    public function getCacheKey(): string
    {
        return App::getLocale() . $this->updated_at->format('Y-m-d H:i:s') . $this->id . md5(serialize($this->relations));
    }

    public function getTimeLeftAttribute(): string
    {
        return now()->diffInDays($this->date_finish);
    }

    public function getTimeStartAttribute(): string
    {
        return now()->diffInDays($this->date_start);
    }

    //Закончилась ли акция
    public function getTimeFinishedAttribute(): bool
    {
        return Carbon::parse($this->date_finish)->isPast();
    }

    //Началась ли акция
    public function getTimeStartedAttribute(): bool
    {
        return Carbon::parse($this->date_start)->isPast();
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

//--
    public function promotionCodeProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_promotion',
            'promotion_id',
            'product_code',
            '',
            'code'
        )->withPivot('product_code')->active()->available();
    }

    public function product_promotion(): HasMany
    {
        return $this->hasMany(ProductPromotion::class, 'promotion_id');
    }

//--

    public function formateDate(string $date): string
    {
        return date('d.m.Y', strtotime($date));
    }

    public function formateTDate(string $date): string
    {
        return date('Y-m-d\TH:i:s', strtotime($date));
    }

    public function getNode(): Tree
    {
        return Tree::where('template', 'promotion')->first();
    }

    public function getUrl($locale = ''): string
    {
        //return route('promotion', $this->getUrlOrSlug($locale));
        return geturl(route('promotion', $this->getUrlOrSlug($locale))); //мультиязычный
    }

    public function scopeFilterTime(Builder $query, ?string $filter): Builder
    {
        match ($filter) {
            'new' => $query->where('created_at', '>=', Carbon::now()->subdays(30)),
            'will_end_soon' => $query->whereBetween('date_finish', [Carbon::now(), Carbon::now()->addDays(7)]),
            default => $query,
        };

        return $query;
    }

    public function scopeNotFinished(Builder $query): Builder
    {
        //return $query->where('date_finish', '>', Carbon::now());
        return $query->when(function ($query) {
            return !empty($query->getModel()->date_finish) && $query->getModel()->date_finish !== '0000-00-00 00:00:00';
        }, function ($query) {
            return $query->where('date_finish', '>', Carbon::now());
        });
    }

    public function filter(): FilterUniversal
    {
        return new FilterUniversal($this);
    }


    public function characteristics()
    {
        return Characteristic::whereIn('id', $this->characteristics)->active();
    }

    public function characteristicsActive()
    {
        return $this->characteristics()->get();
    }
}

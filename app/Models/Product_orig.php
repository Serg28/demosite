<?php

namespace App\Models;

use App\Services\Comment as CommentService;
use App\Services\Compare;
use App\Traits\LikeableTrait;
use App\Traits\RelatedProducts;
use App\Traits\Searchable;
use App\Traits\ViewedTrait;
use Bkwld\Cloner\Cloneable;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product_orig extends BaseModel
{
    use Searchable;
    use ViewedTrait;
    use LikeableTrait;
    use Cloneable;
    use RelatedProducts;

    protected $table = 'products';

    protected $guarded = [];

    protected $with = ['labels', 'status'];

    protected array $cloneable_relations = ['seo', 'characteristics'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function product_join_blocks(): HasMany
    {
        return $this->hasMany(ProductJoinBlock::class)->orderBy('priority');
    }

    public function characteristics(): HasMany
    {
        return $this->hasMany(ProductCharacteristicOption::class)->orderPriority();
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function interestingProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'products_interesting_products',
            'product_id',
            'product_interesting_id'
        );
    }

    public function scopeJoinProduct(Builder $query, bool $join = true): Builder
    {
        return $query->where('is_join', $join);
    }

    public function scopeSortByData(Builder $query, string $data, string $field): Builder
    {
        $sort = ($data == 'priceup') ? 'asc' : 'desc';

        return $query->orderBy($field, $sort);
    }

    public function getUrl(): string
    {
        return geturl('/'.$this->slug);
    }

    public function getNode(): Category
    {
        return $this->category;
    }

    public function getPrice(): int
    {
        return $this->getPriceConvert($this->price);
    }

    public function getPriceOld(): ?int
    {
        if ($this->price_old) {
            return $this->getPriceConvert($this->price_old);
        }

        return null;
    }

    private function getPriceConvert(int $price): int
    {
        $price = $this->userHaveDiscount($price);

        if (setting('konvertirovat-cenu')) {
            return round($price * setting('kurs'));
        }

        return $price;
    }

    public function userHaveDiscount(int $price): int
    {
        $user = app('user');

        if ($user && ($user->discount || $user->discount_cumulative)) {
            $discount = $user->discount > $user->discount_cumulative ? $user->discount : $user->discount_cumulative;

            return round($price - $price * $discount / 100);
        }

        return $price;
    }

    public function getSale(): string
    {
        return 100 - round($this->getPrice() / $this->getPriceOld() * 100).'%';
    }

    public function comment()
    {
        return new CommentService($this);
    }

    public function getArticle(): string
    {
        if ($this->code) {
            return $this->code;
        }

        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getCharacteristicGroup()
    {
        return $this->characteristics->groupBy(function ($item) {
            return $item->characteristic->t('title');
        });
    }

    public function getYoutubeLink(): ?string
    {
        return explode('=', $this->link_to_youtube)[1] ?? null;
    }

    public function checkLike()
    {
        if (app('user')) {
            $cacheLike = Cache::get('like_user_'.app('user')->id.'_product_'.$this->id);

            return $cacheLike
                ? $cacheLike
                : $this->liked(app('user')->id);
        }

        return false;
    }

    public function checkCompare()
    {
        return (new Compare())->check($this);
    }

    public function isNew(): string
    {
        return $this->is_new ? __t('new') : __t('used');
    }

    public function isStock($isSnake = false)
    {
        return $this->product_status_id == 1
            ? $this->asSnake(__t('in stock'), $isSnake)
            : $this->asSnake(__t('out of stock'), $isSnake);
    }

    public function getPicture(): string
    {
        return request()->getSchemeAndHttpHost().$this->getImgPath(500, 500);
    }

    public function asSnake(string $word, bool $status): string
    {
        return $status ? Str::snake($word) : $word;
    }

    public function withoutTag(): string
    {
        return strip_tags($this->t('short_description'));
    }

    public function isAvailability(): bool
    {
        return $this->status && $this->status->report_availability;
    }

    public function getTextFromJson($text, $lang = 'ua')
    {
        $decode = json_decode($text);

        if (! $decode) {
            return '';
        }

        return $decode->$lang;
    }
}

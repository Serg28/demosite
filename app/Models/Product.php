<?php

namespace App\Models;

use App\ProductSearchRule;
use App\ProductsIndexConfigurator;
use App\Enums\ProductStatusEnum;
//use App\Services\Comment as CommentService;
use App\Services\Compare;
use App\Services\DeliveryTime;
use App\Traits\AnaliticProductTrait;
use App\Traits\LikeableTrait;
//use App\Traits\LocalizedPrice;
use App\Traits\RelatedProducts;
//use App\Traits\Searchable;
use Illuminate\Support\Collection;
use Novius\ScoutElastic\Searchable;
use App\Traits\ViewedTrait;
use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Iksaku\Laravel\MassUpdate\MassUpdatable;
use Usamamuneerchaudhary\Commentify\Traits\Commentable;

class Product extends BaseModel
{
    use Searchable;
    use ViewedTrait;
    use LikeableTrait;
    use Cloneable;
    use RelatedProducts;
    use AnaliticProductTrait;
    use Commentable;
    //use LocalizedPrice;

    use MassUpdatable;

    protected $table = 'products';

    protected $guarded = [];

    //protected $with = ['labels', 'status', 'category.parent'];

    //protected $with = ['labels'];

    protected array $cloneable_relations = ['seo', 'characteristics'];

    protected $fillable = [];

    //Поля для вывода в каталоге
    public array $cardFields = [
        'products.id',
        'products.code',
        'products.title',
        'products.slug',
        'products.url',
        'products.price',
        'products.price_old',
        'products.picture',
        'products.other_pictures',
        //'products.arrival_date',
        'products.product_status_id',
        'products.is_active',
        'availability_info',
        'products.quantity',
        'products.picture',
        'products.rating',
        //'products.has_mono_payparts',
        //'products.has_privat_payparts',
        'products.updated_at',
        'products.privat_payparts_count',
        'products.mono_payparts_count',
        'products.category_id',
        'products.ru_url',
        'products.ua_url',
        'products.rating',
        'products.count_comments'
    ];

    protected $indexConfigurator = ProductsIndexConfigurator::class;

    protected $searchRules = [
        ProductSearchRule::class
    ];

    //Отключить ли "гидратацию" модели при возврате ее из индекса elasticsearch
    //При false будут возвращаться также и "виртуальные" атрибуты, как они определены в индексе и перечислены в $fillable
    //При true из индекса возвращается реальная модель, как при обычном eloquent-запросе, без "виртуальных" атрибутов
    public bool $databaseHydrate = true;

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['characteristicsCache','category', 'categories']);
    }

    public function toSearchableArray(): array
    {
        if ($this->title !== null) {
            $this->title = preg_replace("/[\r\n]+/", '\\r\\n', $this->title);
            $this->title = str_replace("\t", '\t', $this->title);
        }
        $article = $this->prepareWord($this->getArticle()) ?? '';

        return [
            'id' => (int)$this->id,
            'title' => $this->title ? strtolower(implode('|', $this->prepareWordsArray($this->title)) . '|' . $article) : '',
            'code' => $article,
            //'part_number' => $this->part_number ?: '',
            'is_active' => (int)($this->is_active) ? 1 : 0,
            'price' => $this->preparePrice(),
            'options' => $this->getListCharacteristics(),
            'category' => $this->getCategories(),
            'top_category' => $this->prepareTopCategory(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product_status_id' => (int)$this->product_status_id,
            'count_views' => (int)$this->count_views,
            'has_picture' => (int)($this->picture) ? 1 : 0,
            'is_sale' => $this->isSale()
        ];
    }

    public function prepareWordsArray($value): array
    {
        $array = is_array($value) ? array_values($value) : json_decode($value, true);

        if (!is_array($array)) {
            return [];
        }

        $output = [];
        foreach ($array as $item) {
            if (!empty($item)) {
                $item = $this->prepareWord($item);
                $output[] = $item;
                $output[] = transliterator_transliterate('Any-Latin; Latin-ASCII', $item); // to latin
                $output[] = transliterator_transliterate('Latin-Russian/BGN', $item); // to rus
            }
        }

        return $output;
    }

    public function prepareWord($string): string
    {
        $replacements = [
            '-' => '_',
            '/' => '|u005C|',
            '+' => '\+',
            '=' => '\=',
            '&' => '\&',
            '|' => '\|',
            '>' => '',
            '<' => '',
            '!' => '\!',
            '(' => '\(',
            ')' => '\)',
            '{' => '\{',
            '}' => '\}',
            '[' => '\[',
            ']' => '\]',
            '^' => '\^',
            '"' => '\"',
            '~' => '\~',
            '*' => '\*',
            '?' => '\?',
            ':' => '\:',
            '\\' => '\\\\',
            '`' => '',
            'ʼ' => '',
            "'" => '',
            '‛' => '',
        ];

        $string = strtolower($string);
        return strtr($string, $replacements);
    }


    private function getListCharacteristics(): Collection
    {
        return $this->characteristics->mapToGroups(function ($item) {
            return [$item->characteristic_id => $item->characteristic_option_id];
        });
    }

    //Получаем текущую и дополнительные категории + всех родителей без корневой категории
    /*public function getCategories(): Collection
    {
        $parents = $this->category->ancestorsAndSelf($this->category_id)
            ->filter(function ($category) {
                return !is_null($category->parent_id);
            })
            ->pluck("id");

        return $this->categories->pluck('id')->push($this->category_id)->merge($parents)->unique();
    }*/
    public function getCategories(): Collection
    {
        $categoryIds = collect();

        // Проверяем и добавляем текущую категорию и её родителей, если $this->category не null
        if (!is_null($this->category)) {
            $parents = $this->category->ancestorsAndSelf($this->category_id)
                ->filter(function ($category) {
                    return !is_null($category->parent_id);
                })
                ->pluck("id");

            $categoryIds = $categoryIds->merge($parents);
        }

        // Добавляем дополнительные категории
        if (!is_null($this->categories)) {
            $categoryIds = $categoryIds->merge($this->categories->pluck('id'));
        }

        // Добавляем текущую категорию
        if (!is_null($this->category_id)) {
            $categoryIds->push($this->category_id);
        }

        return $categoryIds->unique();
    }

    private function prepareTopCategory()
    {
        return $this->getTopCategory() ? $this->getTopCategory()->id : 1;
    }

    private function preparePrice()
    {
        if (method_exists($this, 'getPricesList')) {
            return $this->getPricesList();
        }

        return $this->price;
    }

    public function getCacheKey(): string
    {
        return App::getLocale() . $this->updated_at?->format('Y-m-d H:i:s') . $this->id . md5(serialize($this->relations));
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_id');
    }

    /**
     * Название непосредственной родительской категории
     * @return string
     */
    public function getParentCategoryName(): string
    {
        $parentCategory = $this->getParentCategory();

        return $parentCategory ? $parentCategory->t('title') : '';
    }

    /**
     * Название верхней категории товара, сразу ниже КАТАЛОГа
     * @return string
     */
    public function getTopCategoryName(): string
    {
        $topCategory = $this->getTopCategory();

        return $topCategory ? $topCategory->t('title') : '';
    }

    /**
     * Непосредственная родительская категория
     */
    public function getParentCategory()
    {
        return $this->getNode();
    }

    /**
     * Верхняя категория товара, сразу ниже КАТАЛОГа
     */
    public function getTopCategory()
    {
        if (!$this->category) {
            return null;
        }

        return ($this->category->parent && $this->category->parent->id !== 2) ? $this->category->parent : $this->getParentCategory();
    }

    public function category_product()
    {
        return $this->hasMany(CategoryProduct::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }

    /*public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }*/

    public function product_join_blocks(): HasMany
    {
        return $this->hasMany(ProductJoinBlock::class)->orderBy('priority');
    }

    //Основные характеристики - медленно
    /*
    public function baseCharacteristics(): Builder|HasMany
    {
        return $this->characteristicsCache()
            ->whereHas('characteristic.categories', function ($query) {
                $query->where('is_base', 1);
            })
            ->with([
                'characteristicOption',
                //'characteristic.categoryCharacteristic.group'
            ]);
    }*/

    /*public function __allCharacteristics()
    {
        return $this->characteristicsCache()
            ->select(
                'product_characteristic_options.*',
                'characteristic_options.slug as option_slug',
                'characteristics.title as characteristic_title',
                'characteristic_group_names.title as group_title',
                'characteristic_options.title as option_title'
            )
            ->join("characteristic_options", "product_characteristic_options.characteristic_option_id", "=", "characteristic_options.id")
            ->join('characteristics', 'product_characteristic_options.characteristic_id', '=', 'characteristics.id')
            ->join('category_characteristic', 'characteristics.id', '=', 'category_characteristic.characteristic_id')
            ->join('categories', 'category_characteristic.category_id', '=', 'categories.id')
            ->leftJoin('characteristic_group_names', 'category_characteristic.group_id', '=', 'characteristic_group_names.id')
            ->where('categories.id', $this->category_id);
    }*/

    //Все характеристики товара со значениями
    public function allCharacteristics()
    {
        return $this->characteristicsCache()
            ->select(
                'product_characteristic_options.*',
                'characteristic_options.slug as option_slug',
                'characteristics.title as characteristic_title',
                'characteristic_group_names.title as group_title',
                'characteristic_options.title as option_title',
                'category_characteristic.group_id' // добавляем идентификатор группы
            )
            ->leftJoin('characteristic_options', function ($join) {
                $join->on('product_characteristic_options.characteristic_option_id', '=', 'characteristic_options.id');
            })
            ->join('characteristics', 'product_characteristic_options.characteristic_id', '=', 'characteristics.id')
            ->join('category_characteristic', 'characteristics.id', '=', 'category_characteristic.characteristic_id')
            ->join('categories', 'category_characteristic.category_id', '=', 'categories.id')
            ->leftJoin('characteristic_group_names', 'category_characteristic.group_id', '=', 'characteristic_group_names.id')
            ->where('categories.id', $this->category_id)
            //->where('product_characteristic_options.product_id', $this->id) // Условие для текущего товара
            ->where(function($query) {
                $query->whereNotNull('product_characteristic_options.characteristic_option_id')
                    ->orWhereNotNull('product_characteristic_options.characteristic_option_value');
            });
    }

    //Основные характеристики - быстро. Выводит характеристики-значения, которые в таблице category_characteristic c атрибутом is_base = 1
    public function baseCharacteristics()
    {
        return $this->allCharacteristics()->where('category_characteristic.is_base', 1)->limit(9);
    }

    //Формирование основных характеристик с группировкой по названию группы - аттрибут base_characteristics
    public function getBaseCharacteristicsAttribute()
    {
        return once(function (){
            return $this->baseCharacteristics()
                ->get()
                ->groupBy("characteristic_title")
                ->map(fn($group) => [
                    "title" => $group->first()->t("characteristic_title"),
                    //"values" => $group->map(fn($item) => $item->t("option_title"))->implode(", ")
                    "values" => $group->map(function ($item) {
                        // Если у записи есть значение option_title, используем его,
                        // иначе берем characteristic_option_value
                        return !empty($item->t("option_title")) ? $item->t("option_title") : $item->t('characteristic_option_value');
                    })->implode(", ")
                ]
                );
            //Вариант возврата данных как есть - в шаблоне или контроллере уже формируем вывод по потребности
            //return $this->baseCharacteristics()->get();
        });
    }

    public function characteristics(): HasMany
    {
        return $this->hasMany(ProductCharacteristicOption::class)->orderPriority();
    }

    public function characteristicsCache(): HasMany
    {
        return $this->characteristics()
            ->rememberForever()->cacheTags([
                'characteristics',
                'characteristic_options',
                'product_characteristic_options'
            ]);
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

    public function promoCodes(): BelongsToMany
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_product', 'product_code', 'promo_code_id');
    }

    public function scopeJoinProduct(Builder $query, bool $join = true): Builder
    {
        return $query->where('is_join', $join);
    }

    public function scopenotNullPrice(Builder $query): Builder
    {
        return $query->where('price', '>', 0);
    }

    public function scopeavailable(Builder $query): Builder
    {
        //return $query->where('quantity', '>', 0);
        return $query->where('product_status_id', '!=', ProductStatusEnum::OutOfStock());
    }

    public function scopesortByData(Builder $query, string $data, string $field): Builder
    {
        $sort = ($data == 'priceup') ? 'asc' : 'desc';

        return $query->orderBy($field, $sort);
    }

    public function scopeSortedBy(Builder $query, ?string $sort, ?string $idsOrdered = null): Builder
    {
        match ($sort) {
            'pricedown' => $query->orderBy('price', 'desc'),
            'priceup' => $query->orderBy('price', 'asc'),
            'popularity' => $query->orderBy('count_views', 'desc'),
            'date' => $query->orderBy('created_at', 'desc'),
            default => $idsOrdered ? $query->orderByRaw("FIELD(products.id, {$idsOrdered})") : $query,
        };

        return $query;
    }

    public function scopeInCategories($query, Category $category)
    {
        return $query
            ->leftJoin('category_product', 'category_product.product_id', '=', 'products.id')
            ->where(function ($subquery) use ($category) {
                $subquery->where('category_product.category_id', $category->id)
                    ->orWhere('products.category_id', $category->id);
            });
    }

    public function getUrl($locale = ''): string
    {
        //return geturl('/' . $this->slug); //из коробки - slug как урл
        return geturl('/' . $this->getUrlOrSlug($locale)); //url мультиязычный
    }

    public function getNode(): ?Category
    {
        return once(function (){
            return $this->category;
        });
    }

    //Общее количество товара в наличии. Используется для фронта
    public function getQuantity(): int
    {
        //return $this->quantity;
        //Поскольку мы не ведем проверку кол-ва на фронте, товар либо есть, либо нет
        //Поэтому в случае его наличия ставим заведомо большое число наличия. Иначе - 0.
        //В корзине и везде в логике проверки наличия товара этого достаточно.
        //В случае же необходимости учета нужно возвращать реальное количество товара
        return $this->isActive() ? 1000000000 : 0;
    }

    public function getPrice(): int
    {
        // Если трейт LocalizedPrice применен, вызываем метод из трейта
        if (method_exists($this, 'getTraitPrice')) {
            return $this->getTraitPrice();
        }

        return $this->getPriceConvert($this->price);
    }

    public function getPriceOld(): ?int
    {

        // Если трейт LocalizedPrice применен, вызываем метод из трейта
        if (method_exists($this, 'getTraitOldPrice')) {
            return $this->getTraitOldPrice();
        }

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

    public function isSale()
    {
        return (!empty($this->price_old) && $this->getPriceOld() > $this->getPrice()) ? 1 : 0;
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
        return 100 - round($this->getPrice() / $this->getPriceOld() * 100) . '%';
    }

    public function getArticle(): string
    {
        if ($this->code) {
            return $this->code;
        }
        return '';
        //return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getCharacteristicGroup()
    {
        //Из коробки
        return $this->characteristics->groupBy(function ($item) {
            return $item->characteristic->t('title');
        });
    }

    public function getCharacteristics()
    {
        return once(function(){
            return $this->characteristics->mapWithKeys(fn ($characteristic) => [
                $characteristic->characteristic->slug => [
                    'slug' => $characteristic->characteristic->slug,
                    'title' => $characteristic->characteristic->t('title'),
                    'value' => $characteristic->characteristicOption->t('title')
                ]
            ]);
        });
    }

    public function getCharacteristicsList()
    {
        return $this->characteristics->mapWithKeys(fn ($characteristic) => [
            $characteristic->characteristic->t('title') => $characteristic->characteristicOption->t('title'),
        ]);
    }

    public function getCharacteristic($slug)
    {
        $value = $this->characteristicsCache()->with('characteristicOption')->whereHas(
            'characteristic',
            function ($q) use ($slug) {
                $q->where('slug', $slug);
            }
        )->first();
        return $value->characteristicOption ?? null;
    }

    public function getYoutubeLink(): ?string
    {
        return explode('=', $this->link_to_youtube)[1] ?? null;
    }

    public function checkLike()
    {
        if (app('user')) {
            $cacheLike = Cache::get('like_user_' . app('user')->id . '_product_' . $this->id);

            return $cacheLike ?: $this->liked(app('user')->id);
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

    public function isStock(bool $isSnake = false): string
    {
        //return $this->product_status_id == 1
        return $this->isActive()
            ? $this->asSnake(__t('in stock'), $isSnake)
            : $this->asSnake(__t('out of stock'), $isSnake);
    }

    //Товар активен и доступен для заказа
    public function isActive(): bool
    {
        return once(function (){
            //return $this->getQuantity() && $this->is_active && $this->product_status_id !== 4; //если не в статусе Нет в наличии
            return $this->is_active && !in_array($this->product_status_id, [ProductStatusEnum::OutOfStock(), ProductStatusEnum::Expected()]); //если не в статусе Нет в наличии
        });
    }

    public function getPicture(): string
    {
        return request()->getSchemeAndHttpHost() . $this->getImgPath(500, 500);
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

    /*public function getTextFromJson(string $text, string $lang = 'ua'): string
    {
        $decode = json_decode($text);

        if (!$decode) {
            return '';
        }

        return $decode->$lang;
    }*/
    public function getTextFromJson(string $text, string $lang = 'ua'): string
    {
        $decode = json_decode($text);

        return $decode && isset($decode->$lang) ? $decode->$lang : '';
    }

    public function getAvailabilityInfo()
    {
        return collect(json_decode($this->availability_info, true));
    }


    //Есть ли товар на складе с указанным ID
    public function hasWarehouseId($id): bool
    {
        if (!$availabilityInfo = json_decode($this->availability_info, true)) {
            return false;
        }
        $filteredInfo = array_filter($availabilityInfo, function ($item) use ($id) {
            return isset($item['id']) && $item['id'] == $id;
        });
        return !empty($filteredInfo);
    }

    //Есть ли товар хотя бы на одном из указанных в массиве ID складов
    public function hasWarehouseIds(array $ids): bool
    {
        if (!$availabilityInfo = json_decode($this->availability_info, true)) {
            return false;
        }
        $filteredInfo = array_filter($availabilityInfo, function ($item) use ($ids) {
            return isset($item['id']) && in_array($item['id'], $ids);
        });
        return count($filteredInfo) > 0;
    }

    public function getDeliveryTime(): array
    {
        return (new DeliveryTime())->getDeliveryTimeForProduct($this);
    }


    public function characteristicOptions(): HasManyThrough
    {
        return $this->hasManyThrough(
            CharacteristicOption::class,
            ProductCharacteristicOption::class,
            'product_id',
            'id',
            'id',
            'characteristic_option_id'
        );
    }

    public function getCharacteristicsGroupedAttribute()
    {
        return once(function (){
            return $this->characteristicOptions->groupBy(function ($item) {
                //return $item->characteristic->t('title');
                return $item->characteristic->slug;
            });
        });
    }

    public function getStatusNameAttribute()
    {
        return $this->status?->t('title');
    }
    public function getStatusDescriptionAttribute()
    {
        return $this->status?->t('description');
    }

    public function getStatusColorAttribute()
    {
        return $this->status?->color ?? '';
    }

    //Бренд товара
    public function getBrandAttribute()
    {
        /*return cache()->remember('product_brand_' . $this->getCacheKey(), now()->addMinutes(86400), function () {
            $brandOptions = $this->characteristicsGrouped['brand'] ?? false;
            if ($brandOptions) {
                return $brandOptions->first();
            }

            return false;
        });*/
        return once(function() {
            return cache()->remember('product_vendor_' . $this->getCacheKey(), now()->addDay(), function () {
                // Оптимизируем выбор производителя с помощью фильтрации по ключу группы
                $brandOptions = $this->characteristicsGrouped['proizvoditel'] ?? false;

                if ($brandOptions) {
                    // Получаем первую характеристику производителя
                    $characteristicOption = $brandOptions->first();

                    // Проверяем наличие метода связи vendor и возвращаем объект Vendor, если существует
                    return $characteristicOption?->vendor ?: false;
                }

                return false;
            });
        });
    }

    //Название бренда товара
    public function getBrandName() : string
    {
        return $this->brand ? $this->brand->t('title') : '';
    }

    public function getOtherPictures()
    {
        return cache()->rememberForever('product.otherPictures.' . $this->getCacheKey(), function () {
            return $this->getOtherImgWithOriginal('other_pictures', ['w' => 155, 'h' => 155]);
        });
    }

    public function getFirstOtherPictureAttribute()
    {
        // Получаем изображения или пустой массив, если вернулось null
        $images = $this->getOtherPictures() ?? [];

        // Проверяем, что массив с изображениями не пуст
        $firstKey = array_key_first($images);

        // Возвращаем изображение по ключу или изображение по умолчанию
        return $firstKey ? ($images[$firstKey] ?? setting('no-foto')) : setting('no-foto');
    }

    public function scopeCardFields($query)
    {
        return $query->select($this->cardFields);
    }

    /**
     * Определяет, доступен ли продукт для оплаты частями ПриватБанка.
     *
     * @return bool
     */
    public function getIsPrivatPaypartsAttribute(): bool
    {
        //TODO: нужно ли проверять has_privat_payparts  ?
        //return $this->has_privat_payparts == 1 && $this->privat_payparts_count > 0 && $this->getQuantity() > 0;
        return $this->getPrivatPartsCount() && $this->getQuantity() > 0;
    }

    /**
     * Определяет, доступен ли продукт для оплаты частями Монобанка.
     *
     * @return bool
     */
    public function getIsMonobankPaypartsAttribute(): bool
    {
        //TODO: нужно ли проверять has_mono_payparts  ?
        //return $this->has_mono_payparts == 1 && $this->mono_payparts_count > 0 && $this->getQuantity() > 0;
        return $this->getMonoPartsCount() && $this->getQuantity() > 0;
    }

    /**
     * Возвращает количество частей рассрочки Приватбанка, если такая возможность доступна для товара
     * Если нет, возвращается false
     *
     * @return bool|int
     */
    public function getPrivatPartsCount(): bool|int
    {
        //TODO: нужно ли проверять has_privat_payparts  ?
        //return $this->has_privat_payparts == 1 ? $this->privat_payparts_count : setting('privat-payparts-default-count') ?? 0;
        return (int)$this->privat_payparts_count > 0 ? $this->privat_payparts_count : setting('privat-payparts-default-count') ?? 0;
    }

    /**
     * Возвращает количество частей рассрочки Монобанка, если такая возможность доступна для товара
     * Если нет, возвращается false
     *
     * @return bool|int
     */
    public function getMonoPartsCount(): bool|int
    {
        //TODO: нужно ли проверять has_mono_payparts  ?
        //return $this->has_mono_payparts == 1 ? $this->mono_payparts_count : setting('monobank-payparts-default-count') ?? 0;
        return (int)$this->mono_payparts_count > 0 ? $this->mono_payparts_count : setting('monobank-payparts-default-count') ?? 0;
    }

    public function news() {
        return $this->belongsToMany(News::class, 'news_products', 'news_id')
            ->withPivot('category_id');
    }

    //Возвращаем путь к blade-шаблону в зависимости от типа товара у категории. Или же возвращает стандартные шаблоны
    //Напр., если у категории тип товара выбран как parts, а suffix = index, то путь будет product.parts.index.
    // А в случае его отсутствия вернет стандартный шаблон product.index
    public function getTemplate(string $suffix): string
    {
        // Получаем тип товара для текущей модели
        $parentCategory = $this->getParentCategory();
        $templateType = $parentCategory ? $parentCategory->getCategoryTemplateType() : '';

        // Проверяем существование шаблона
        $template = 'product' . ($templateType ? ".$templateType" : '') . ".$suffix";
        $templateExists = view()->exists($template);

        // Возвращаем путь в зависимости от наличия типа товара и существования шаблона
        return $templateType && $templateExists ? $template: "product.$suffix";
    }

    //Возврат шаблона страницы карточки товара
    public function getTemplateProduct(): string
    {
        return $this->getTemplate('index');
    }
}

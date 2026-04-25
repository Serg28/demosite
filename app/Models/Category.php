<?php

namespace App\Models;

use App\Events\CategorySaved;
use App\Models\MorphOne\Seo;
use App\Services\Filters\Filter;
use App\Traits\PrepareModelFields;
//use App\Traits\SeoCustomUrlTrait;
use App\Traits\SeoTrait;
use App\Traits\Sluggable;
use App\Traits\SlugUrlFieldTrait;
use App\Traits\TreeRecurcive;
use App\Traits\UpdateTreeDepth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Vis\Builder\Tree as TreeBuilder;

class Category extends TreeBuilder
{
    use SeoTrait;
    use TreeRecurcive;
    //use SeoCustomUrlTrait;
    use SlugUrlFieldTrait;
    use UpdateTreeDepth;
    use PrepareModelFields;
    use Sluggable;

    protected $table = 'categories';

    protected $guarded = [];

    //public $timestamps = false;

    private $categories = 'сategories';

    //protected $with = ['characteristics'];

    protected $dispatchesEvents = [
        'saved' => CategorySaved::class,
        'deleting' => CategorySaved::class,
    ];

    public function getCacheTags(): array
    {
        return ['categories', 'menu_catalog'];
    }

    public function getCacheKey(): string
    {
        return App::getLocale() . $this->updated_at . $this->id . md5(serialize($this->relations));
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seo');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function guarantee(): HasOne
    {
        return $this->HasOne(Guarantee::class);
    }

    public function googleCategory(): BelongsTo
    {
        return $this->belongsTo(GoogleCategory::class, 'google_id');
    }

    public function hotlineCategory(): BelongsTo
    {
        return $this->belongsTo(HotlineCategory::class, 'hotline_id');
    }

    public function products_related(): belongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('product_id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class);
        //->orderBy('category_characteristic.id', 'asc'); //для новой таблицы характ. у категории
    }

    //Характеристики категории, которые выводятся в фильтре
    public function characteristicsFilter(): BelongsToMany
    {
        return $this->characteristics()->where(function ($query) {
            $query->active()->filterable();
        });
    }

    public function category_characteristics(): hasMany
    {
        return $this->hasMany(CategoryCharacteristic::class)
            //->orderBy('characteristic_id', 'asc'); //так из коробки
            ->orderBy('priority', 'asc'); //для новой таблицы характ. у категории
    }

    //Характеристики категории, которые выводятся в фильтре
    public function characteristicsForFilter(): Collection
    {
        return $this->characteristicsFilter()
            ->withPivot('name', 'is_closed', 'priority', 'is_filter')
            ->remember(20 * 60) // Кэшируем результат на 20 минут (20 * 60 секунд)
            ->cacheTags(['characteristics', $this->categories])
            ->orderBy('category_characteristic.priority', 'asc') // Для новой таблицы характеристик у категории
            ->get();
    }

    public function characteristicsForFilterExist(): bool
    {
        return $this->characteristicsFilter()->withPivot('name', 'is_closed', 'priority', 'is_filter')
            ->cacheTags(['characteristics', $this->categories])
            ->exists();
    }

    public function characteristicsProducts(): HasManyThrough
    {
        return $this->hasManyThrough(ProductCharacteristicOption::class, Product::class);
    }

    public function characteristicRelative(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class, 'characteristic_id');
    }

    public function characteristicsForProductRelative(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class, 'category_characteristic_relative')
            ->orderBy('category_characteristic_relative.id', 'asc');
    }

    public function getNode(): Category
    {
        return $this;
    }

    /**
     * Верхняя категория товара, сразу ниже КАТАЛОГа
     */
    public function getTopCategory()
    {
        return $this->getAncestorsAndSelf()->firstWhere('depth', 1);
    }

    public function hasChildren(): bool
    {
        //Оптимальнее
        return once(function() {
            return \DB::table('categories')
                ->where('parent_id', $this->id)
                ->exists();
        });
        //Медленнее
        //return $this->children()->select('id')->rememberForever()->cacheTags(['categories'])->exists();
    }

    //Из коробки - url = slug
    /*public function getUrl(): string
    {
        return getUrl(str_replace('//', '/', parent::getUrl()));
    }*/

    //Вариант url мультиязычный вложенный
    //--------------
    /*public function getUrl($locale = '')
    {
        $this->_nodeUrl = $this->getGeneratedUrl($locale);

        if (str_contains($this->_nodeUrl, 'http')) {
            return $this->_nodeUrl;
        }

        return getUrl(str_replace('//', '/', '/' . $this->_nodeUrl));
    }*/


    //Вариант мультиязычный slug - НЕ вложенные урлы, только урл...

    public function getUrl($locale = '')
    {
        //return geturl(route('catalog', $this->getUrlOrSlug($locale))); //мультиязычный урл
        return geturl($this->getUrlOrSlug($locale) ?? '/'); //мультиязычный урл
    }

    public function getAncestorsAndSelf()
    {
        return $this->defaultOrder()->remember(20 * 60)->ancestorsAndSelf($this->id, ['id', 'title', 'parent_id', 'lft', 'rgt', 'slug', 'url']);
    }

    private function getGeneratedUrlInCache($locale = ''): string
    {
        $all = $this->getAncestorsAndSelf();
        $slugs = [];

        foreach ($all as $node) {
            $url = ($node->getUrlOrSlug($locale));
            if ($url == '/') {
                continue;
            }

            $slugs[] = $url;
        }

        return implode('/', $slugs);
    }

    public function getGeneratedUrl($locale = '')
    {
        $tags = $this->getCacheTags();
        $locale = $locale ?: App::getLocale();

        if ($tags && $this->fileDefinition) {
            return Cache::tags($tags)->remember(
                $this->fileDefinition . '_' . $this->id . '_' . $locale,
                20 * 60, // Кэшируем результат на 20 минут (20 * 60 секунд)
                function () use ($locale) {
                    return $this->getGeneratedUrlInCache($locale);
                }
            );
        }

        return $this->getGeneratedUrlInCache($locale);
    }

    //----------------

    public static function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', '1');
    }

    public function getMenu(): Collection
    {
        $model = $this;

        $menuAll = Cache::tags([$this->categories])->remember(
            $this->categories,
            20 * 60, // Кэшируем результат на 20 минут (20 * 60 секунд)
            function () use ($model) {
                return $model::with('children')
                    ->defaultOrder()
                    ->where('is_active', 1)
                    ->get()
                    ->toTree();
            }
        );

        if (isset($menuAll[0])) {
            return $menuAll[0]->children;
        }

        return collect([]);
    }

    public function getSiteMenu(int $rootId = 1, bool $onlySelf = false): Collection
    {
        return Cache::tags($this->getCacheTags())->remember(
            "tree{$rootId}_" . class_basename(get_class($this)) . "_{$onlySelf}",
            now()->addDay(),
            fn () => $this::with(['children'])
                ->defaultOrder()
                ->active()
                ->when($onlySelf, fn ($q) => $q->whereParentId($rootId))
                ->descendantsAndSelf($rootId)
                ->toTree($rootId)
        ) ?? collect([]);
    }

    public function filter(): Filter
    {
        return new Filter($this);
    }

    public function getIcoImg(): ?string
    {
        if ($this->ico) {
            return "<img loading=\"lazy\" src='" . glide($this->ico, ['w' => 20, 'h' => 20]) . "' width='20' height='20' >";
        }

        return null;
    }

    public function getTextFromJson(string $text, string $lang = ''): string
    {
        $lang = ($lang) ? $lang : App::getLocale();
        $decode = json_decode($text);

        if (!$decode) {
            return '';
        }

        return $decode->$lang;
    }

    //Возвращаем название типа товара, используемого для формирования пути к шаблонам blade для вывода категорий в сайте.
    //Напр., если тип товара в категории - auto, то берем шаблоны из папки auto и т.д.
    //Для корректной работы нужно подключить трейт App\Traits\CategoryTemplatable Без него шаблон будет стандартный
    public function getCategoryTemplateType()
    {
        return $this->category_template_name ?? null;
    }


    //Возвращаем путь к blade-шаблону в зависимости от типа товара у категории. Или же возвращает стандартные шаблоны
    public function getTemplate(string $suffix): string
    {
        // Получаем тип товара для текущей модели
        $templateType = $this->getCategoryTemplateType();

        // Проверяем существование шаблона
        $templateExists = view()->exists("category.$templateType.$suffix");

        // Возвращаем путь в зависимости от наличия типа товара и существования шаблона
        return $templateType && $templateExists ? "category.$templateType.$suffix" : "category.$suffix";
    }

    public function getTemplateIndex(): string
    {
        return $this->getTemplate('index');
    }

    public function getTemplateCatalog(): string
    {
        return $this->getTemplate('catalog');
    }

    public function getTemplateIndexWithoutFilter(): string
    {
        return $this->getTemplate('index_without_filter');
    }
}

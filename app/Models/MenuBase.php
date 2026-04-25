<?php

namespace App\Models;

use App\Events\MenuSaved;
use App\Traits\SeoCustomUrlTrait;
use App\Traits\SeoTrait;
use App\Traits\UpdateTreeDepth;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\QueryBuilder;
use Vis\Builder\Tree as TreeBuilder;

class MenuBase extends TreeBuilder
{
    use SeoTrait;
    use SeoCustomUrlTrait;
    use UpdateTreeDepth;

    protected $fillable = [];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'saved' => MenuSaved::class,
        'deleting' => MenuSaved::class,
    ];

    public function menu(): MorphTo
    {
        return $this->morphTo('menu');
    }

    public function scopeActive(QueryBuilder $builder): QueryBuilder
    {
        return $builder->where('is_active', 1);
    }

    public function getUrl($locale = ''): string
    {
        if ($this->menu_type && $this->menu) {
            return $this->menu->getUrl($locale);
        }

        if ($this->t('url')) {
            return $this->t('url');
        }

        if ($this->t('url_external')) {
            return $this->t('url_external');
        }

        return '/';
    }

    public function getMenu(): Collection
    {
        $model = $this;

        $menuAll = Cache::tags([
            'tree',
            'menu_header',
            'menu_footer'
        ])->remember(
            'tree' . class_basename(get_class($this)) . App::getLocale(),
            now()->addDay(),
            function () use ($model) {
                return $model::with(['menu', 'children'])
                    ->defaultOrder()
                    ->active()
                    ->get()
                    ->toTree();
            }
        );

        if (isset($menuAll[0])) {
            return $menuAll[0]->children;
        }

        return collect([]);
    }

    public function getParentMenu(int $rootId = 1): Collection
    {
        $model = $this;
        $menuAll = Cache::tags($model->getCacheTags())->remember(
            'tree' . class_basename(get_class($this)) .$rootId. App::getLocale(),
            now()->addDay(),
            function () use ($model, $rootId) {
                return $model::with(['menu', 'children'])
                    ->defaultOrder()
                    ->active()
                    ->descendantsOf($rootId)
                    ->toTree($rootId);
            }
        );
        return $menuAll ?? collect([]);
    }
}

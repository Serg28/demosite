<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use App\Events\MenuCatalogSaved;

class MenuCatalog extends MenuBase
{
    protected $table = 'menu_catalog';

    protected $dispatchesEvents = [
        'saved' => MenuCatalogSaved::class,
        'deleted' => MenuCatalogSaved::class,
    ];

    public function getCacheTags()
    {
        return ['tree', 'menu_catalog'];
    }

    public function getSiteMenu(int $rootId = 1, bool $onlySelf = false): Collection
    {
        return Cache::tags($this->getCacheTags())->remember(
            "tree{$rootId}_" . class_basename(get_class($this)) /*. App::getLocale()*/ . "_{$onlySelf}",
            now()->addDay(),
            fn () => $this::with(['menu', 'children'])
                ->defaultOrder()
                ->active()
                ->when($onlySelf, fn ($q) => $q->whereParentId($rootId))
                ->descendantsAndSelf($rootId)
                ->toTree($rootId)
        ) ?? collect([]);
    }
}

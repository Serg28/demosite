<?php

namespace App\Listeners\MenuCatalog;

use App\Events\MenuCatalogSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class MenuCatalogClearCache implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MenuCatalogSaved $event): void
    {
        //Сбросим кеш
        Cache::tags($event->menuCatalog->getCacheTags())->flush();
        //Перегенерируем меню - чтобы и урлы, и само меню категорий попало в кеш
        $event->menuCatalog->getSiteMenu(1);
    }
}

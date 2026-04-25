<?php

namespace App\Http\ViewComposers;

use App\Models\News;
use App\Models\Tree;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LastNewsComposer
{
    public function compose(View $view): void
    {
        /*$newsLast = News::active()->where('id', '!=', $view->page->id)->latest()->limit(3)
            ->rememberForever()->cacheTags(['news'])
            ->get();*/
        $newsLast = News::active()->latest()->where('id', '!=', $view->page->id)->limit(4)->get();
        $allNewsFolder = Tree::template('news')
            ->rememberForever()->cacheTags(['tree'])
            ->first();

        $cacheKey = App::getLocale() . $view->id . md5($newsLast) . md5($newsLast->pluck('updated_at'));
        $cacheTags = ['lastnews'];

        $view->with(compact('newsLast', 'allNewsFolder', 'cacheKey', 'cacheTags'));
    }
}

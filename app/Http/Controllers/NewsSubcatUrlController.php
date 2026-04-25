<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementViewNews;
use App\Models\News;
use App\Models\Tag;
use App\Repository\NewsRepository;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class NewsSubcatUrlController_ extends TreeController
{
    public $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function index(): View
    {
        $page = $this->node;
        //$news = $this->newsRepository->filterByTag(request('tag'));
        $category_id = ($page->id !== 10) ? $page->id : '';
        $news = $this->newsRepository->filterByCategory($category_id);
        $tags = Tag::active()->get();
        $categories = $this->newsRepository->getAllCategories(3);

        return view('news.index', compact('page', 'news', 'tags', 'categories'));
    }

    public function page(string $slug): View
    {
        $catpage = $this->newsRepository->getCategoryBySlug($slug);
        if (! $catpage) {
            $page = $this->newsRepository->findBySlug($slug, ['tags']);
            $newsLast = $this->newsRepository->getLatest(3, $page->id);
            $newsByTags = $this->newsRepository->getSimilarNewsByTag($page, 2);
            $tags = Tag::active()->get();

            $this->incrementView($page);

            return view('news.page', compact('page', 'newsLast', 'tags', 'newsByTags'));
        } else {
            $catpage->firstOrFail();
        }
    }

    public function subcatpage(string $category, string $slug): View
    {
        $catpage = $this->newsRepository->findCategoryBySlug($category);
        $page = $this->newsRepository->findByCategoryAndSlug($catpage->id, $slug, ['tags']);
        $newsLast = $this->newsRepository->getLatest(3, $page->id);
        $newsByTags = $this->newsRepository->getSimilarNewsByTag($page, 2);
        $tags = Tag::active()->get();

        $this->incrementView($page);

        return view('news.page', compact('page', 'newsLast', 'tags', 'newsByTags'));
    }

    private function incrementView(News $page): void
    {
        IncrementViewNews::dispatch($page);
    }
}

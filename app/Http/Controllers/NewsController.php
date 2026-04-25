<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementViewNews;
use App\Models\News;
use App\Models\Tag;
use App\Repository\NewsRepository;
use App\Services\NewsByTags;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class NewsController extends TreeController
{
    public $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function index(): View
    {
        $page = $this->node;
//        $tag = request('tag');
//        //$category_id = $page->id;
//        $category_id = ($page->id !== 10) ? $page->id : '';
//        if ($tag && $category_id) {
//            $news = $this->newsRepository->filterByCategoryAndTag($category_id, $tag);
//        } elseif ($tag && !$category_id) {
//            $news = $this->newsRepository->filterByTag(request('tag'));
//        } elseif (!$tag && $category_id) {
//            $news = $this->newsRepository->filterByCategory($category_id);
//        } else {
//            $news = $this->newsRepository->filterAllNews();
//        }
//
//        //$news = $this->newsRepository->filterByCategory($category_id);
//        $tags = Tag::active()->get();
//        $categories = $this->newsRepository->getAllCategories(10);
//        $tagname = ($tag) ? $tags->where('slug', $tag)->first() : '';

        return view('news.index',compact('page'));
        //return view('news.index', compact('page',  'newsRepository'));
    }

    //Со вложенными урл
    /*public function page(string $slug): View
    {
        $catpage = $this->newsRepository->getCategoryBySlug($slug);
        if (!$catpage) {
            $page = $this->newsRepository->findBySlug($slug, ['tags']);
            $newsLast = $this->newsRepository->getLatest(3, $page->id);
            $newsByTags = $this->newsRepository->getSimilarNewsByTag($page, 2);
            $tags = Tag::active()->get();

            $this->incrementView($page);

            return view('news.page', compact('page', 'newsLast', 'tags', 'newsByTags'));
        } else {
            $catpage->firstOrFail();
        }
    }*/

    //C урл одного уровня вложенности
    public function page(?string $slug = null): View
    {
        $page = $this->newsRepository->findBySlug($slug, ['tags']);
        $newsByTags = $this->newsRepository->getSimilarNewsByTag($page, 4);
        $newsLast = $this->newsRepository->getLatest(6, $page->id);
        $tags = Tag::active()->get();
        //$nextPage = $this->newsRepository->getNextNews($page->id, $page->category->id);
        $categories = $this->newsRepository->getAllCategories(10);

        $this->incrementView($page);

        return view('news.page', compact('page', 'tags', 'newsByTags', 'categories', 'newsLast'));
    }

    //Со вложенными урл
    /*public function subcatpage(string $category, string $slug): View
    {
        $catpage = $this->newsRepository->findCategoryBySlug($category);
        $page = $this->newsRepository->findByCategoryAndSlug($catpage->id,$slug,['tags']);
        $newsLast = $this->newsRepository->getLatest(3, $page->id);
        $newsByTags = $this->newsRepository->getSimilarNewsByTag($page, 2);
        $tags = Tag::active()->get();

        $this->incrementView($page);

        return view('news.page', compact('page', 'newsLast', 'tags', 'newsByTags'));
    }*/

    //C урл одного уровня вложенности
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

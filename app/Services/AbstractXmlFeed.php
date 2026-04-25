<?php

namespace App\Services;

use App\Enums\FeedTypeEnum;
use App\Models\Category;
use App\Models\Feed;
use App\Models\FeedCategory;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

abstract class AbstractXmlFeed
{
    protected string $view;

    public function __construct()
    {
        $this->view = 'xml.'.mb_strtolower(class_basename($this));
    }

    public function render(Feed $feed): Response
    {
        $categoriesIds = $feed->categories->pluck('id');

        $products = $this->getProducts($categoriesIds, $feed);
        $categories = $this->getCategories($categoriesIds, $feed);
        $description = $feed->t('description');

        return response()
            ->view($this->view, compact('products', 'categories', 'description'))
            ->header('Content-Type', 'text/xml');
    }

    public function renderToFile(Feed $feed)
    {
        $categoriesIds = $feed->categories->pluck('id');

        $products = $this->getProducts($categoriesIds, $feed);
        $categories = $this->getCategories($categoriesIds, $feed);
        $description = $feed->t('description');

        return view($this->view, compact('products', 'categories', 'description', 'feed'))->render();
    }

    protected function getProducts(Collection $categoriesIds, Feed $feed): Collection
    {
        //$products = Product::query()->active();
        $products = Product::with('category', 'seo', 'characteristics.characteristicOption', 'characteristics.characteristic')->active()->where('price', '>', 0)->where('quantity', '>', 0);

        $products->when($feed->type === FeedTypeEnum::categories->name, function ($q) use ($categoriesIds) {
            return $q->whereIn('category_id', $categoriesIds);
        });

        $products->when(count($feed->options) && $feed->characteristic_id, function ($q) use ($feed) {
            $productsOptions = ProductCharacteristicOption::where('characteristic_id', $feed->characteristic_id)
                ->whereIn('characteristic_option_id', $feed->options()->pluck('characteristic_options.id'))
                ->pluck('product_id');

            return $q->whereIn('products.id', $productsOptions);
        });
        if (! empty($feed->limit)) {
            $products->limit($feed->limit);
        }
        //return $products->limit(10000)->get();
        return $products->get();
    }

    //Оригинал. Работает без рубрикаторов гугла и хотлайн
    protected function getCategories(Collection $categoriesIds, Feed $feed): Collection
    {
        $categories = Category::query()->active();

        $categories->when($feed->type === FeedTypeEnum::categories->name, function ($q) use ($categoriesIds) {
            return $q->whereIn('id', $categoriesIds);
        });

        return $categories->get();
    }

    /*
    protected function getCategories(Collection $categoriesIds, Feed $feed): Collection
    {
        return FeedCategory::with(['hotlineCategory', 'googleCategory']) // Загружаем связи hotlineCategory и googleCategory
        ->where('feed_id', $feed->id) // Указываем ID фида
        ->when($feed->type === FeedTypeEnum::categories->name, function ($q) use ($categoriesIds) {
            return $q->whereIn('category_id', $categoriesIds); // Фильтруем по ID категорий
        })
        ->get()
            ->map(function ($feedCategory) {
                // Модифицируем объект FeedCategory, добавляя нужные свойства
                $feedCategory->title = $feedCategory->category->title; // Имя категории
                $feedCategory->id = $feedCategory->category->id; // ID категории
                $feedCategory->parent_id = $feedCategory->category->parent_id; // Родительская категория

                // Удаляем оригинальный объект category, если он вам не нужен
                unset($feedCategory->category);

                return $feedCategory; // Возвращаем измененный объект
            });
    }
*/






}

<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Characteristic;
use App\ValueObjects\BreadcrumbItem;
use Kalnoy\Nestedset\Collection;

class BreadcrumbsService
{
    /**
     * Хлібні крихти для сторінки категорії.
     * Один SQL-запит за lft/rgt. Кеш 1 год — дерево змінюється рідко.
     * Остання крихта: seo_h1 (без {{pagenumber}}) замість title.
     *
     * @return list<BreadcrumbItem>
     */
    public function forCategory(Category $category): array
    {
        /** @var Collection<int, Category> $ancestors */
        $ancestors = cache()->remember(
            "breadcrumbs.category.{$category->id}",
            3600,
            fn () => Category::defaultOrder()
                ->ancestorsAndSelf($category->id, ['id', 'title', 'slug', 'lft', 'rgt', 'depth', 'parent_id'])
        );

        $items = [new BreadcrumbItem(__t('Головна'), geturl('/'))];

        foreach ($ancestors as $i => $ancestor) {
            $isLast = $i === count($ancestors) - 1;
            $title = $isLast ? $ancestor->getSeoBreadcrumbTitle() : $ancestor->t('title');

            $items[] = new BreadcrumbItem($title, $ancestor->getUrl());
        }

        return $items;
    }

    /**
     * Хлібні крихти для категорії з активним фільтром (SEO filter pages).
     * Додає категорію як посилання + поточну фільтровану крихту без URL.
     *
     * @return list<BreadcrumbItem>
     */
    public function forFilteredCategory(Category $category, string $filterTitle): array
    {
        $items = $this->forCategory($category);

        // Остання крихта з forCategory() — сама категорія з URL.
        // Замінюємо на: [категорія-посилання] > [фільтр без URL]
        array_pop($items);
        $items[] = new BreadcrumbItem($category->t('title'), $category->getUrl());
        $items[] = new BreadcrumbItem($filterTitle);

        return $items;
    }

    /**
     * Хлібні крихти для сторінки значення характеристики.
     * Головна > Бренди (або назва розділу) > Значення.
     * API-сумісний з BreadcrumbsOptionsComposer linecore-demo.
     *
     * @return list<BreadcrumbItem>
     */
    public function forCharacteristic(Characteristic $characteristic, string $valueName): array
    {
        return [
            new BreadcrumbItem(__t('Головна'), geturl('/')),
            new BreadcrumbItem($characteristic->t('title'), $characteristic->getUrl()),
            new BreadcrumbItem($valueName),
        ];
    }

    /**
     * Хлібні крихти для сторінки бренду/виробника.
     * Головна > Бренди > Назва бренду.
     * API-сумісний з BreadcrumbsVendorComposer linecore-demo.
     *
     * @return list<BreadcrumbItem>
     */
    public function forBrand(string $brandName, ?string $brandsUrl = null): array
    {
        return [
            new BreadcrumbItem(__t('Головна'), geturl('/')),
            new BreadcrumbItem(__t('Бренди'), $brandsUrl ?? geturl('/brands')),
            new BreadcrumbItem($brandName),
        ];
    }

    /**
     * Прості хлібні крихти для статичних сторінок (доставка, про нас тощо).
    /**
     * Хлібні крихти для картки товару: Головна > Категорія > Товар.
     *
     * @return list<BreadcrumbItem>
     */
    public function forProduct(\App\Models\Product $product): array
    {
        $items = [new BreadcrumbItem(__t('Головна'), geturl('/'))];

        if ($product->category_id) {
            $category = cache()->remember(
                "breadcrumbs.product_category.{$product->category_id}",
                3600,
                fn () => \App\Models\Category::find($product->category_id, ['id', 'title', 'slug'])
            );

            if ($category) {
                $items[] = new BreadcrumbItem($category->t('title'), $category->getUrl());
            }
        }

        $items[] = new BreadcrumbItem($product->getSeoBreadcrumbTitle());

        return $items;
    }

    /**
     * Хлібні крихти для сторінок особистого кабінету.
     * Головна > Кабінет > [Назва сторінки]
     *
     * @return list<BreadcrumbItem>
     */
    public function forProfile(string $pageTitle = ''): array
    {
        $items = [
            new BreadcrumbItem(__t('Головна'), geturl('/')),
            new BreadcrumbItem(__t('Кабінет'), route('profile.index')),
        ];

        if ($pageTitle !== '') {
            $items[] = new BreadcrumbItem($pageTitle);
        }

        return $items;
    }

    /**
     * $pages: ['url' => 'Заголовок', ...]
     *
     * @param  array<string, string>  $pages
     * @return list<BreadcrumbItem>
     */
    public function simple(string $currentTitle = '', array $pages = []): array
    {
        $items = [new BreadcrumbItem(__t('Головна'), geturl('/'))];

        foreach ($pages as $url => $title) {
            $items[] = new BreadcrumbItem($title, $url);
        }

        if ($currentTitle !== '') {
            $items[] = new BreadcrumbItem($currentTitle);
        }

        return $items;
    }
}

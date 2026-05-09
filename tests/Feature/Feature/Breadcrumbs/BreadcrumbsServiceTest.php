<?php

namespace Tests\Feature\Feature\Breadcrumbs;

use App\Models\Category;
use App\Services\BreadcrumbsService;
use App\ValueObjects\BreadcrumbItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_for_category_starts_with_home(): void
    {
        $category = Category::factory()->create(['slug' => 'laptops', 'title' => json_encode(['ua' => 'Ноутбуки'])]);
        $service = app(BreadcrumbsService::class);

        $items = $service->forCategory($category);

        $this->assertInstanceOf(BreadcrumbItem::class, $items[0]);
        $this->assertNotEmpty($items[0]->url);
    }

    public function test_for_category_includes_category_item(): void
    {
        $category = Category::factory()->create(['slug' => 'laptops', 'title' => json_encode(['ua' => 'Ноутбуки'])]);
        $service = app(BreadcrumbsService::class);

        $items = $service->forCategory($category);

        $this->assertCount(2, $items);
        $this->assertStringContainsString('catalog', $items[1]->url);
        $this->assertNotEmpty($items[1]->title);
    }

    public function test_simple_builds_items(): void
    {
        $service = app(BreadcrumbsService::class);

        $items = $service->simple('Доставка', []);

        $this->assertCount(2, $items);
        $this->assertSame('Доставка', $items[1]->title);
        $this->assertSame('', $items[1]->url);
    }

    public function test_simple_with_pages(): void
    {
        $service = app(BreadcrumbsService::class);

        $items = $service->simple('Сторінка', ['/about' => 'Про нас']);

        $this->assertCount(3, $items);
        $this->assertSame('Про нас', $items[1]->title);
        $this->assertSame('/about', $items[1]->url);
    }

    public function test_breadcrumb_item_without_url(): void
    {
        $item = new BreadcrumbItem('Заголовок');

        $this->assertSame('Заголовок', $item->title);
        $this->assertSame('', $item->url);
    }
}

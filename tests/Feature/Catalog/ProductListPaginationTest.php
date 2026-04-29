<?php

namespace Tests\Feature\Catalog;

use App\Livewire\Catalog\ProductList;
use App\Models\Category;
use App\Models\Product;
use App\Services\TypeSenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductListPaginationTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();

        $this->mock(TypeSenseService::class, function ($mock) {
            $mock->shouldReceive('search')
                ->andReturnUsing(function (string $query, array $filters, int $page, int $pageSize) {
                    $lastPage = 5;

                    return [
                        'products'     => Product::factory($pageSize)->make()->all(),
                        'total'        => $pageSize * $lastPage,
                        'per_page'     => $pageSize,
                        'current_page' => $page,
                        'last_page'    => $lastPage,
                        'has_more'     => $page < $lastPage,
                    ];
                });
        });
    }

    public function test_renders_product_grid(): void
    {
        Livewire::test(ProductList::class, ['category' => $this->category])
            ->assertStatus(200)
            ->assertSeeHtml('js-product-grid');
    }

    public function test_starts_on_page_one(): void
    {
        $component = Livewire::test(ProductList::class, ['category' => $this->category]);

        $this->assertSame(1, $component->get('page'));
    }

    public function test_initial_page_set_via_mount(): void
    {
        Livewire::test(ProductList::class, [
            'category'    => $this->category,
            'initialPage' => 3,
        ])->assertSet('page', 3);
    }

    public function test_initial_page_minimum_is_one(): void
    {
        Livewire::test(ProductList::class, [
            'category'    => $this->category,
            'initialPage' => -5,
        ])->assertSet('page', 1);
    }

    public function test_filters_updated_resets_to_page_one(): void
    {
        Livewire::test(ProductList::class, [
            'category'    => $this->category,
            'initialPage' => 3,
        ])
            ->assertSet('page', 3)
            ->dispatch('filtersUpdated', filters: ['min_price' => 100])
            ->assertSet('page', 1);
    }

    public function test_filters_updated_dispatches_product_list_reset(): void
    {
        Livewire::test(ProductList::class, ['category' => $this->category])
            ->dispatch('filtersUpdated', filters: [])
            ->assertDispatched('product-list-reset');
    }

    public function test_sort_mount_sets_sort_params(): void
    {
        Livewire::test(ProductList::class, [
            'category' => $this->category,
            'sortBy'   => 'price',
            'sortDir'  => 'asc',
        ])
            ->assertSet('sortBy', 'price')
            ->assertSet('sortDir', 'asc');
    }

    public function test_sort_mount_starts_on_page_one(): void
    {
        Livewire::test(ProductList::class, [
            'category' => $this->category,
            'sortBy'   => 'price',
            'sortDir'  => 'asc',
        ])->assertSet('page', 1);
    }

    public function test_pagination_links_rendered_as_anchors(): void
    {
        Livewire::test(ProductList::class, ['category' => $this->category])
            ->assertSeeHtml('js-load-more')
            ->assertSeeHtml('<a href=');
    }

    public function test_empty_state_shown_when_no_products(): void
    {
        $this->mock(TypeSenseService::class, function ($mock) {
            $mock->shouldReceive('search')->andReturn([
                'products'     => [],
                'total'        => 0,
                'per_page'     => 24,
                'current_page' => 1,
                'last_page'    => 1,
                'has_more'     => false,
            ]);
        });

        Livewire::test(ProductList::class, ['category' => $this->category])
            ->assertSee(__t('Товари не знайдені'));
    }
}

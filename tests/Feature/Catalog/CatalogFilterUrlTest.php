<?php

namespace Tests\Feature\Catalog;

use App\Livewire\Catalog\Facets;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Services\FacetService;
use App\Services\TypeSenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogFilterUrlTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Characteristic $colorChar;

    private CharacteristicOption $redOption;

    private CharacteristicOption $blueOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['slug' => 'phones']);

        $this->colorChar = Characteristic::factory()->create([
            'slug' => 'color',
            'is_active' => true,
            'is_range_type' => false,
            'priority' => 1,
        ]);
        $this->category->characteristics()->attach($this->colorChar);

        $this->redOption = CharacteristicOption::factory()->create([
            'characteristic_id' => $this->colorChar->id,
            'slug' => 'red',
        ]);
        $this->blueOption = CharacteristicOption::factory()->create([
            'characteristic_id' => $this->colorChar->id,
            'slug' => 'blue',
        ]);

        $this->mockFacetService();
    }

    private function mockFacetService(): void
    {
        $this->mock(FacetService::class, function ($mock) {
            $mock->shouldReceive('getFacetsForCategory')->andReturn([
                'price' => ['min' => 0, 'max' => 0, 'current_min' => null, 'current_max' => null],
                'characteristics' => [
                    [
                        'characteristic_id' => $this->colorChar->id,
                        'characteristic_slug' => 'color',
                        'characteristic_title' => 'Color',
                        'is_range_type' => false,
                        'options' => [
                            ['id' => $this->redOption->id,  'slug' => 'red',  'title' => 'Red',  'count' => 10, 'is_active' => false, 'is_disabled' => false],
                            ['id' => $this->blueOption->id, 'slug' => 'blue', 'title' => 'Blue', 'count' => 5,  'is_active' => false, 'is_disabled' => false],
                        ],
                    ],
                ],
                'clear_url' => '/catalog/phones/',
            ]);
        });
    }

    // ─── Controller: filter resolution from PATH ──────────────────────────────

    public function test_catalog_page_loads_without_filters(): void
    {
        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertViewHas('initialFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null, 'in_stock' => false]);
    }

    public function test_controller_resolves_single_option_slug_from_path(): void
    {
        $this->get('/catalog/'.$this->category->slug.'/color=red/')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [$this->colorChar->id => [$this->redOption->id]],
                'min_price' => null,
                'max_price' => null,
                'in_stock'  => false,
            ]);
    }

    public function test_controller_resolves_multiple_option_slugs_from_path(): void
    {
        $this->get('/catalog/'.$this->category->slug.'/color=red,blue/')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [$this->colorChar->id => [$this->redOption->id, $this->blueOption->id]],
                'min_price' => null,
                'max_price' => null,
                'in_stock'  => false,
            ]);
    }

    public function test_controller_resolves_price_from_path(): void
    {
        $this->get('/catalog/'.$this->category->slug.'/price=100-500/')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [],
                'min_price' => 100,
                'max_price' => 500,
                'in_stock'  => false,
            ]);
    }

    public function test_controller_ignores_unknown_segments(): void
    {
        $this->get('/catalog/'.$this->category->slug.'/unknown-param=value/')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null, 'in_stock' => false]);
    }

    public function test_controller_resolves_in_stock_from_path(): void
    {
        $this->get('/catalog/'.$this->category->slug.'/in_stock=1/')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null, 'in_stock' => true]);
    }

    public function test_controller_passes_base_path_to_view(): void
    {
        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertViewHas('basePath', '/catalog/'.$this->category->slug);
    }

    // ─── Facets: rendering & options ─────────────────────────────────────────

    public function test_facets_renders_option_data_attributes(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
        ])
            ->assertSeeHtml('data-char-slug="color"')
            ->assertSeeHtml('data-opt-slug="red"')
            ->assertSeeHtml('data-opt-slug="blue"');
    }

    public function test_facets_renders_checkbox_inputs(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
        ])
            ->assertSeeHtml('class="js-filter-input')
            ->assertSeeHtml('data-char-slug=');
    }

    public function test_facets_initializes_from_path(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
            'initialFiltersPath' => 'color=red/',
        ])
            ->assertSet('filtersPath', 'color=red/');
    }

    public function test_filter_changed_updates_filters_path(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
        ])
            ->dispatch('filter-changed', path: '/catalog/phones/color=red/')
            ->assertSet('filtersPath', 'color=red/');
    }

    public function test_filter_changed_dispatches_filters_updated(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
        ])
            ->dispatch('filter-changed', path: '/catalog/phones/color=red/')
            ->assertDispatched('filtersUpdated');
    }

    public function test_filter_changed_with_empty_path_clears_filters(): void
    {
        Livewire::test(Facets::class, [
            'category' => $this->category,
            'basePath' => '/catalog/phones',
            'initialFiltersPath' => 'color=red/',
        ])
            ->dispatch('filter-changed', path: '/catalog/phones/')
            ->assertSet('filtersPath', '');
    }

    // ─── SEO: noindex on filter path ─────────────────────────────────────────

    public function test_seo_noindex_nofollow_on_clean_url_in_non_production(): void
    {
        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertSee('noindex, nofollow');
    }

    public function test_seo_no_robots_on_clean_url_in_production(): void
    {
        config(['app.domain' => 'localhost']);
        $this->partialMock(TypeSenseService::class, fn ($m) => $m->shouldReceive('count')->andReturn(5));

        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertDontSee('noindex');
    }

    public function test_seo_noindex_follow_on_filter_path_in_production(): void
    {
        config(['app.domain' => 'localhost']);
        $this->partialMock(TypeSenseService::class, fn ($m) => $m->shouldReceive('count')->andReturn(5));

        $this->get('/catalog/'.$this->category->slug.'/color=red/')
            ->assertStatus(200)
            ->assertSee('noindex, follow');
    }
}

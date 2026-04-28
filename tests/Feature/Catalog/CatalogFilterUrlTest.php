<?php

namespace Tests\Feature\Catalog;

use App\Livewire\Catalog\Facets;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Services\FacetService;
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

        // Stub FacetService so tests don't need TypeSense
        $this->mock(FacetService::class, function ($mock) {
            $mock->shouldReceive('getFacetsForCategory')->andReturn([
                'price' => ['min' => 0, 'max' => 0],
                'characteristics' => [
                    [
                        'facet_id' => 'characteristic_'.$this->colorChar->id,
                        'characteristic_id' => $this->colorChar->id,
                        'characteristic_slug' => 'color',
                        'characteristic_title' => 'Color',
                        'is_range_type' => false,
                        'options' => [
                            ['id' => $this->redOption->id, 'slug' => 'red', 'title' => 'Red', 'count' => 10],
                            ['id' => $this->blueOption->id, 'slug' => 'blue', 'title' => 'Blue', 'count' => 5],
                        ],
                        'has_more' => false,
                    ],
                ],
            ]);
        });
    }

    // ─── Controller: filter resolution from URL ───────────────────────────────

    public function test_catalog_page_loads_without_filters(): void
    {
        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertViewHas('initialFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null]);
    }

    public function test_controller_resolves_single_option_slug_to_id(): void
    {
        $this->get('/catalog/'.$this->category->slug.'?color=red')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [$this->colorChar->id => [$this->redOption->id]],
                'min_price' => null,
                'max_price' => null,
            ]);
    }

    public function test_controller_resolves_multiple_option_slugs(): void
    {
        $this->get('/catalog/'.$this->category->slug.'?color=red,blue')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [$this->colorChar->id => [$this->redOption->id, $this->blueOption->id]],
                'min_price' => null,
                'max_price' => null,
            ]);
    }

    public function test_controller_resolves_price_params(): void
    {
        $this->get('/catalog/'.$this->category->slug.'?min_price=100&max_price=500')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', [
                'characteristics' => [],
                'min_price' => 100,
                'max_price' => 500,
            ]);
    }

    public function test_controller_ignores_unknown_char_slugs(): void
    {
        $this->get('/catalog/'.$this->category->slug.'?unknown-param=value')
            ->assertStatus(200)
            ->assertViewHas('initialFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null]);
    }

    // ─── Facets: rendering & options ─────────────────────────────────────────

    public function test_facets_renders_option_as_anchor_link(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->assertSeeHtml('href="?color=red"')
            ->assertSeeHtml('href="?color=blue"');
    }

    public function test_facets_uses_wire_click_prevent_on_options(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->assertSeeHtml('wire:click.prevent="toggleOption(');
    }

    public function test_facets_initializes_from_initial_filters(): void
    {
        $initialFilters = [
            'characteristics' => [$this->colorChar->id => [$this->redOption->id]],
            'min_price' => null,
            'max_price' => null,
        ];

        Livewire::test(Facets::class, ['category' => $this->category, 'initialFilters' => $initialFilters])
            ->assertSet('currentFilters', $initialFilters);
    }

    public function test_toggle_option_adds_option_to_filters(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->call('toggleOption', $this->colorChar->id, $this->redOption->id)
            ->assertSet('currentFilters.characteristics', [$this->colorChar->id => [$this->redOption->id]]);
    }

    public function test_toggle_option_removes_already_selected_option(): void
    {
        $initialFilters = [
            'characteristics' => [$this->colorChar->id => [$this->redOption->id]],
            'min_price' => null,
            'max_price' => null,
        ];

        Livewire::test(Facets::class, ['category' => $this->category, 'initialFilters' => $initialFilters])
            ->call('toggleOption', $this->colorChar->id, $this->redOption->id)
            ->assertSet('currentFilters.characteristics', []);
    }

    public function test_toggle_option_dispatches_filters_updated(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->call('toggleOption', $this->colorChar->id, $this->redOption->id)
            ->assertDispatched('filtersUpdated');
    }

    public function test_toggle_option_dispatches_update_filter_url(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->call('toggleOption', $this->colorChar->id, $this->redOption->id)
            ->assertDispatched('updateFilterUrl');
    }

    public function test_clear_filters_resets_state(): void
    {
        $initialFilters = [
            'characteristics' => [$this->colorChar->id => [$this->redOption->id]],
            'min_price' => 100,
            'max_price' => 500,
        ];

        Livewire::test(Facets::class, ['category' => $this->category, 'initialFilters' => $initialFilters])
            ->call('clearFilters')
            ->assertSet('currentFilters', ['characteristics' => [], 'min_price' => null, 'max_price' => null]);
    }

    public function test_clear_filters_dispatches_update_filter_url_with_empty_params(): void
    {
        Livewire::test(Facets::class, ['category' => $this->category])
            ->call('clearFilters')
            ->assertDispatched('updateFilterUrl', params: []);
    }

    // ─── SEO: noindex on filter pages ────────────────────────────────────────

    public function test_seo_no_robots_meta_on_clean_category_url_in_production(): void
    {
        config(['app.env' => 'production']);

        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertDontSee('noindex');
    }

    public function test_seo_noindex_follow_on_filter_url_in_production(): void
    {
        config(['app.env' => 'production']);

        $this->get('/catalog/'.$this->category->slug.'?color=red')
            ->assertStatus(200)
            ->assertSee('noindex, follow');
    }

    public function test_seo_noindex_nofollow_on_any_url_in_non_production(): void
    {
        $this->get('/catalog/'.$this->category->slug)
            ->assertStatus(200)
            ->assertSee('noindex, nofollow');
    }
}

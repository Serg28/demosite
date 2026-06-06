<?php

namespace Tests\Feature\Compare;

use App\Livewire\Compare\Lists;
use App\Models\Product;
use App\Services\CompareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompareListsTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_for_guest(): void
    {
        $this->get(route('compare.index'))
            ->assertOk()
            ->assertSeeLivewire(Lists::class);
    }

    public function test_shows_empty_state_when_nothing_to_compare(): void
    {
        Livewire::test(Lists::class)
            ->assertSee(__t('Немає товарів для порівняння'));
    }

    public function test_shows_products_in_compare(): void
    {
        $product = Product::factory()->create(['title' => 'Compare Product']);
        $compare = app(CompareService::class);
        $compare->add($product);

        Livewire::test(Lists::class)
            ->assertSee('Compare Product');
    }

    public function test_clear_empties_compare_list(): void
    {
        $product = Product::factory()->create();
        $compare = app(CompareService::class);
        $compare->add($product);

        Livewire::test(Lists::class)
            ->call('clear')
            ->assertOk();

        $this->assertSame(0, $compare->count());
    }

    public function test_total_count_reflects_compare_service(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $compare  = app(CompareService::class);
        $compare->add($product1);
        $compare->add($product2);

        $component = Livewire::test(Lists::class);

        $this->assertSame(2, $component->get('totalCount'));
    }

    public function test_refresh_on_update_compare_count_event(): void
    {
        Livewire::test(Lists::class)
            ->dispatch('updateCompareCount', count: 0)
            ->assertOk();
    }
}

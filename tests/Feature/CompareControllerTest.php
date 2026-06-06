<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\CompareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── add ───────────────────────────────────────────────────

    public function test_add_product_to_compare(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson(route('compare.add', $product));

        $response->assertOk()
            ->assertJsonFragment(['status' => 'success', 'inCompare' => true, 'count' => 1]);
    }

    public function test_add_returns_error_when_limit_reached(): void
    {
        $products = Product::factory()->count(5)->create();

        // Додаємо 4 товари
        foreach ($products->take(4) as $p) {
            $this->postJson(route('compare.add', $p));
        }

        // 5-й — має повернути помилку
        $response = $this->postJson(route('compare.add', $products->last()));

        $response->assertOk()
            ->assertJsonFragment(['status' => 'error', 'count' => 4]);
    }

    // ─── delete ────────────────────────────────────────────────

    public function test_delete_product_from_compare(): void
    {
        $product = Product::factory()->create();
        $this->postJson(route('compare.add', $product));

        $response = $this->postJson(route('compare.delete', $product));

        $response->assertOk()
            ->assertJsonFragment(['status' => 'success', 'inCompare' => false, 'count' => 0]);
    }

    public function test_delete_non_existing_product_succeeds(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('compare.delete', $product))
            ->assertOk()
            ->assertJsonFragment(['status' => 'success', 'count' => 0]);
    }

    // ─── status ────────────────────────────────────────────────

    public function test_status_returns_compare_ids(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $this->postJson(route('compare.add', $product1));

        $response = $this->postJson(route('compare.status'), [
            'productIds' => [$product1->id, $product2->id],
        ]);

        $response->assertOk();
        $inCompare = $response->json('inCompare');

        $this->assertContains($product1->id, $inCompare);
        $this->assertNotContains($product2->id, $inCompare);
    }

    // ─── max items ─────────────────────────────────────────────

    public function test_cannot_add_more_than_4_products(): void
    {
        $products = Product::factory()->count(5)->create();
        $compare  = app(CompareService::class);

        foreach ($products->take(4) as $p) {
            $this->postJson(route('compare.add', $p));
        }

        $this->assertSame(4, $compare->count());

        $this->postJson(route('compare.add', $products->last()));
        $this->assertSame(4, $compare->count());
    }
}
